<?php

namespace WolfSellers\Bopis\Block\Address;

use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Block\Address\Edit;
use Magento\Customer\Helper\Address;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\Model\Session;
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Cache\Type\Config;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\View\Element\Template\Context;

/**
 *
 */
class Form extends Edit {

    /**
     * @var AddressMetadataInterface|null
     */
    private ?AddressMetadataInterface $addressMetadata;

    /**
     * @param Context $context
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param EncoderInterface $jsonEncoder
     * @param Config $configCacheType
     * @param \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory
     * @param CollectionFactory $countryCollectionFactory
     * @param Session $customerSession
     * @param AddressRepositoryInterface $addressRepository
     * @param AddressInterfaceFactory $addressDataFactory
     * @param CurrentCustomer $currentCustomer
     * @param DataObjectHelper $dataObjectHelper
     * @param array $data
     * @param AddressMetadataInterface|null $addressMetadata
     * @param Address|null $addressHelper
     */
    public function __construct(Context $context, \Magento\Directory\Helper\Data $directoryHelper, EncoderInterface $jsonEncoder, Config $configCacheType, \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory, CollectionFactory $countryCollectionFactory, Session $customerSession, AddressRepositoryInterface $addressRepository, AddressInterfaceFactory $addressDataFactory, CurrentCustomer $currentCustomer, DataObjectHelper $dataObjectHelper, array $data = [], AddressMetadataInterface $addressMetadata = null, Address $addressHelper = null)
    {
        parent::__construct($context, $directoryHelper, $jsonEncoder, $configCacheType, $regionCollectionFactory, $countryCollectionFactory, $customerSession, $addressRepository, $addressDataFactory, $currentCustomer, $dataObjectHelper, $data, $addressMetadata, $addressHelper);
        $this->addressMetadata = $addressMetadata;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return "";
    }

    /**
     * @return $this|Form
     */
    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set($this->getTitle());

        if ($postedData = $this->_customerSession->getAddressFormData(true)) {
            $postedData['region'] = [
                'region_id' => isset($postedData['region_id']) ? $postedData['region_id'] : null,
                'region' => $postedData['region'],
            ];
            $this->dataObjectHelper->populateWithArray(
                $this->_address,
                $postedData,
                AddressInterface::class
            );
        }
        $this->precheckRequiredAttributes();
        return $this;
    }

    /**
     * Precheck attributes that may be required in attribute configuration.
     *
     * @return void
     */
    private function precheckRequiredAttributes()
    {
        $precheckAttributes = $this->getData('check_attributes_on_render');
        $requiredAttributesPrechecked = [];
        if (!empty($precheckAttributes) && is_array($precheckAttributes)) {
            foreach ($precheckAttributes as $attributeCode) {
                $attributeMetadata = $this->addressMetadata->getAttributeMetadata($attributeCode);
                if ($attributeMetadata && $attributeMetadata->isRequired()) {
                    $requiredAttributesPrechecked[$attributeCode] = $attributeCode;
                }
            }
        }
        $this->setData('required_attributes_prechecked', $requiredAttributesPrechecked);
    }

    /**
     * Return the country Id.
     *
     * @return int|null|string
     */
    public function getCountryId()
    {
        $parent = get_parent_class(Edit::class);
        return $parent::getCountryId();
    }

}

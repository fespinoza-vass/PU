<?php

namespace WolfSellers\Bopis\Helper;

use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper {
    const COUNTRY_CODE_PATH = 'general/country/default';

    /**
     * @param AddressMetadataInterface
     */
    private AddressMetadataInterface $addressMetadata;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var SourceRepositoryInterface
     */
    private SourceRepositoryInterface $sourceRepository;
    private \Magento\Eav\Model\Config $eavConfig;

    /**
     * @param Context $context
     * @param AddressMetadataInterface $addressMetadata
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SourceRepositoryInterface $sourceRepository
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(
        Context $context,
        AddressMetadataInterface $addressMetadata,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SourceRepositoryInterface $sourceRepository,
        \Magento\Eav\Model\Config $eavConfig
    ) {
        parent::__construct($context);

        $this->addressMetadata = $addressMetadata;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sourceRepository = $sourceRepository;
        $this->eavConfig = $eavConfig;
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getAddressTypes(): array {
        $options = $this->addressMetadata->getAttributeMetadata('tipo_direccion')->getOptions();

        $optionsArray = [];

        foreach ($options as $option) {
            $optionsArray[$option->getValue()] = $option->getLabel();
        }

        return $optionsArray;
    }

    /**
     * @return string
     */
    public function getLoggedInUrl(): string {
        return $this->_getUrl('customer/account/login');
    }

    /**
     * @param string $sourceCode
     * @return SourceInterface|null
     */
    public function getSource(string $sourceCode): ?SourceInterface {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('source_code', $sourceCode)
            ->create();
        $sourceData = $this->sourceRepository->getList($searchCriteria);

        $sources = $sourceData->getItems();

        foreach ($sources as $source) {
            return $source;
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function getCurrentCountry() {
        return $this->scopeConfig->getValue(
            self::COUNTRY_CODE_PATH,
            ScopeInterface::SCOPE_WEBSITES
        );
    }

    public function getAllRegion(){
        $attributeCode = "region";
        $attribute = $this->eavConfig->getAttribute('customer_address', $attributeCode);
        $options = $attribute->getSource()->getAllOptions();
        die(var_export($options));
        $arr = [];
        foreach ($options as $option) {
            if ($option['value'] > 0) {
                $arr[] = $option;
            }
        }
    }
}

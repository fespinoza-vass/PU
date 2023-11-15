<?php

namespace WolfSellers\Bopis\ViewModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use WolfSellers\Bopis\Helper\RealStates;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class GeneralOrder implements ArgumentInterface
{
    /**
     * @param RealStates $_realStates
     * @param RedirectInterface $redirect
     * @param SourceRepositoryInterface $_sourceRepository
     * @param SearchCriteriaBuilder $_searchCriteriaBuilder
     * @param CustomerRepositoryInterface $customerRepository
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        protected RealStates                  $_realStates,
        protected RedirectInterface           $redirect,
        protected SourceRepositoryInterface   $_sourceRepository,
        protected SearchCriteriaBuilder       $_searchCriteriaBuilder,
        protected CustomerRepositoryInterface $customerRepository,
        protected OrderRepositoryInterface    $orderRepository
    )
    {
    }

    /**
     * @param $shippingMethodCode
     * @return string
     */
    public function getShippingMethodTitle($shippingMethodCode)
    {
        return $this->_realStates->getShippingMethodTitle($shippingMethodCode);
    }

    /**
     * @param $status
     * @return string|null
     */
    public function getStateLabel($status): ?string
    {
        if (!$status) {
            return $status;
        }
        return $this->_realStates->getStateLabel($status);
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->redirect->getRefererUrl();
    }

    /**
     * @param $sourceCode
     * @return string
     */
    public function getOrderSourceName($sourceCode): string
    {
        if (!$sourceCode || $sourceCode == '') return '';

        $this->_searchCriteriaBuilder->addFilter('source_code', $sourceCode);
        $searchCriteria = $this->_searchCriteriaBuilder->create();

        $searchCriteriaResult = $this->_sourceRepository->getList($searchCriteria);
        $sources = $searchCriteriaResult->getItems();

        $source = current($sources);

        if (!$source) return $sourceCode;

        return $source->getName();
    }

    /**
     * @param $attributeCode
     * @param $value
     * @return bool|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getRealAddrOptionValue($attributeCode, $value): bool|string
    {
        return $this->_realStates->getRealAddrOptionValue('customer_address', $attributeCode, $value);
    }

    /**
     * @param $horarioDeEntrega
     * @return string
     */
    public function getSchedule($horarioDeEntrega)
    {
        return $this->_realStates->getSchedule($horarioDeEntrega);
    }

    /**
     * @param $customerId
     * @param bool $whitType
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getCustomerIdentificacion($customerId, bool $whitType = true): string
    {
        $identification_type = $this->getCustomerAttributeValue($customerId, 'identificacion');
        $identification_number = $this->getCustomerAttributeValue($customerId, 'numero_de_identificacion');

        if ($whitType) {
            $type = $this->_realStates->getRealAddrOptionValue('customer', 'identificacion', $identification_type);
            return ($type ? $type . ' - ' : '') . $identification_number;
        }


        return $identification_number;
    }

    /**
     * @param $customerId
     * @param $attr
     * @return mixed|string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getCustomerAttributeValue($customerId, $attr)
    {
        $customer = $this->customerRepository->getById($customerId);
        $attribute = $customer->getCustomAttribute($attr);

        if (!$attribute) return '';

        return $attribute->getValue();
    }

    /**
     * @param $customerId
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getCustomerData($customerId)
    {
        return $this->customerRepository->getById($customerId);
    }

    /**
     * @return string
     */
    public function getInStoreCode(): string
    {
        return \WolfSellers\Bopis\Model\ResourceModel\AbstractBopisCollection::PICKUP_SHIPPING_METHOD;
    }

    /**
     * @param $orderId
     * @return array
     * @throws LocalizedException
     */
    public function getCustomerOrderBillingInformation($orderId)
    {
        $order = $this->orderRepository->get($orderId);

        $typeId = $this->_realStates->getRealAddrOptionValue(
            'customer',
            'identificacion',
            $order->getCustomerIdentificacion()
        );

        return [
            'name' => $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname(),
            'email' => $order->getCustomerEmail(),
            'tel' => $order->getCustomerTelefono(),
            'type_id' => $typeId,
            'id_number' => $order->getCustomerNumeroDeIdentificacion()
        ];
    }
}

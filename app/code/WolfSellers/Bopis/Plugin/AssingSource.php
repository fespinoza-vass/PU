<?php

namespace WolfSellers\Bopis\Plugin;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Model\OrderFactory;
use Magento\InventoryInStorePickupSales\Model\ResourceModel\OrderPickupLocation\GetPickupLocationCodeByOrderId;
use Magento\Framework\Api\SearchCriteriaBuilder;
use WolfSellers\Bopis\Model\ResourceModel\AbstractBopisCollection;
use WolfSellers\DireccionesTiendas\Api\DireccionesTiendasRepositoryInterface;
use WolfSellers\DireccionesTiendas\Api\Data\DireccionesTiendasInterface;
use WolfSellers\OrderQR\Logger\Logger;

class AssingSource
{
    const PARAM_TO_FILTER = 'colony';

    /**
     * @param GetPickupLocationCodeByOrderId $_getPickupLocationCodeByOrderId
     * @param SearchCriteriaBuilder $_searchCriteriaBuilder
     * @param DireccionesTiendasRepositoryInterface $direccionesTiendasRepository
     * @param Logger $_logger
     */
    public function __construct(
        protected GetPickupLocationCodeByOrderId        $_getPickupLocationCodeByOrderId,
        protected SearchCriteriaBuilder                 $_searchCriteriaBuilder,
        protected DireccionesTiendasRepositoryInterface $direccionesTiendasRepository,
        protected Logger                                $_logger
    )
    {
    }

    /**
     * @param OrderManagementInterface $subject
     * @param OrderInterface $result
     * @return OrderInterface
     */
    public function afterPlace(OrderManagementInterface $subject, OrderInterface $result): OrderInterface
    {
        $order = $result;

        try {
            $colony = $order->getShippingAddress()->getData(self::PARAM_TO_FILTER);

            $searchCriteria = $this->_searchCriteriaBuilder
                ->addFilter(DireccionesTiendasInterface::DISTRITO, $colony)
                ->create();
            $sources = $this->direccionesTiendasRepository->getList($searchCriteria);

            foreach ($sources->getItems() as $source) {
                $currentSourceCode = $source->getTienda();
            }

            /** Regular delivery always comes from the default source. */
            $sourceCode = match ($order->getShippingMethod()) {
                AbstractBopisCollection::PICKUP_SHIPPING_METHOD =>
                $this->_getPickupLocationCodeByOrderId->execute($order->getEntityId()),
                AbstractBopisCollection::FAST_SHIPPING_METHOD =>
                    $currentSourceCode ?? AbstractBopisCollection::DEFAULT_BOPIS_SOURCE_CODE,
                default =>
                AbstractBopisCollection::DEFAULT_BOPIS_SOURCE_CODE,
            };

            $order->setData('source_code', $sourceCode);

        } catch (\Throwable $error) {
            $this->_logger->error($error->getMessage());
        }

        return $result;
    }
}

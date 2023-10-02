<?php

namespace WolfSellers\Bopis\Plugin;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Model\OrderFactory;
use Magento\InventoryInStorePickupSales\Model\ResourceModel\OrderPickupLocation\GetPickupLocationCodeByOrderId;
use WolfSellers\Bopis\Model\ResourceModel\AbstractBopisCollection;
use WolfSellers\OrderQR\Logger\Logger;

class AssingSource
{
    const DEFAULT_BOPIS_SOURCE_CODE = '1';

    /**
     * @param OrderFactory $_orderRepository
     * @param Logger $_logger
     */
    public function __construct(
        protected OrderFactory $_orderRepository,
        protected GetPickupLocationCodeByOrderId $_getPickupLocationCodeByOrderId,
        protected Logger $_logger
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
            $sourceCode = self::DEFAULT_BOPIS_SOURCE_CODE;

            if($order->getShippingMethod() == AbstractBopisCollection::PICKUP_SHIPPING_METHOD)
            {
                $sourceCode = $this->_getPickupLocationCodeByOrderId->execute($order->getEntityId());
            }

            $order->setData('source_code', $sourceCode);

        } catch (\Throwable $error) {
            $this->_logger->error($error->getMessage());
        }

        return $result;
    }
}

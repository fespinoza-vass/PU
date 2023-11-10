<?php

namespace WolfSellers\Checkout\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Model\OrderFactory;

class SuccessOrder implements ArgumentInterface
{
    const SHIPPING_METHOD_FOR_QRCODE = "instore_pickup";

    /**
     * @param OrderFactory $orderFactory
     */
    public function __construct(
        protected OrderFactory $orderFactory
    )
    {
    }

    /**
     * @param $orderId
     * @return bool
     */
    public function isPickup($orderId): bool
    {
        $order = $this->orderFactory->create()->loadByIncrementId($orderId);
        return boolval(($order->getShippingMethod() == self::SHIPPING_METHOD_FOR_QRCODE));
    }
}

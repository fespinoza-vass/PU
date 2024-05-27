<?php

namespace WolfSellers\EnvioRapido\Model;

/**
 *
 */
class DeliveryTimeExtension implements \Magento\Quote\Api\Data\ShippingMethodExtensionInterface
{
    /**
     * @var
     */
    private $deliveryTime;

    /**
     * @param $deliveryTime
     */
    public function __construct($deliveryTime)
    {
        $this->deliveryTime = $deliveryTime;
    }

    /**
     * @return string|null
     */
    public function getDeliveryTime()
    {
        return $this->deliveryTime;
    }

    /**
     * @param $deliveryTime
     * @return void
     */
    public function setDeliveryTime($deliveryTime)
    {
        $this->deliveryTime = $deliveryTime;
    }
}

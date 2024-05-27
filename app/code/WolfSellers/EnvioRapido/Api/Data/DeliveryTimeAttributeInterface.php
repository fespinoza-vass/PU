<?php

namespace WolfSellers\EnvioRapido\Api\Data;

/**
 *
 */
interface DeliveryTimeAttributeInterface
{
    /**
     *
     */
    const VALUE = 'delivery_time';

    /**
     * Return value.
     *
     * @return string|null
     */
    public function getDeliveryTime();

    /**
     * Set value.
     *
     * @param string|null $deliveryTime
     * @return $this
     */
    public function setDeliveryTime($deliveryTime);
}

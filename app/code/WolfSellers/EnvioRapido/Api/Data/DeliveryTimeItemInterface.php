<?php

namespace WolfSellers\EnvioRapido\Api\Data;

/**
 * Gift Card data
 *
 * @codeCoverageIgnore
 * @api
 * @since 101.0.0
 */
interface DeliveryTimeItemInterface
{
    /**
     * Get Label
     *
     * @return string
     * @since 101.0.0
     */
    public function getLabel();

    /**
     * Set Id
     *
     * @param string $label
     * @return $this
     * @since 101.0.0
     */
    public function setLabel($label);

    /**
     * Get Code
     *
     * @return string
     * @since 101.0.0
     */
    public function getOptionValue();

    /**
     * Set Code
     *
     * @param string $optionValue
     * @return $this
     * @since 101.0.0
     */
    public function setOptionValue($optionValue);

}

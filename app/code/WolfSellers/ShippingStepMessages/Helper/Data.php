<?php

namespace WolfSellers\ShippingStepMessages\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

/**
 * @class Data
 */
class Data extends AbstractHelper
{
    /**
     * Path to the configuration for district settings 1.
     */
    const XML_PATH_INSTORE_PICKUP_OPENINGS = 'regular_shipping/openings_1/';

    /**
     * Path to the configuration for district settings 2.
     */
    const XML_PATH_INSTORE_PICKUP_OPENINGS_2 = 'regular_shipping/openings_2/';

    /**
     * Retrieves the configured locations.
     *
     * @return array
     */
    public function getConfiguredLocations()
    {
        $locationsString = $this->scopeConfig->getValue(
            self::XML_PATH_INSTORE_PICKUP_OPENINGS . 'location',
            ScopeInterface::SCOPE_STORE
        );

        return !empty($locationsString) ? explode(',', $locationsString) : [];
    }

    /**
     * Retrieves the configured sending hours message.
     *
     * @return string
     */
    public function getSendingHoursMessage()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_INSTORE_PICKUP_OPENINGS . 'sending_hours',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieves the configured delivery time message.
     *
     * @return string
     */
    public function getDeliveryTimeMessage()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_INSTORE_PICKUP_OPENINGS . 'delivery_time_message',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieves the configured rest sending hours message.
     *
     * @return string
     */
    public function getRestSendingHoursMessage()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_INSTORE_PICKUP_OPENINGS . 'rest_sending_hours',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieves the configured rest delivery time message.
     *
     * @return string
     */
    public function getRestDeliveryTimeMessage()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_INSTORE_PICKUP_OPENINGS . 'rest_delivery_time_message',
            ScopeInterface::SCOPE_STORE
        );
    }




    /**
     * Retrieves the configured locations.
     *
     * @return array
     */
    public function getConfiguredLocationsTwo()
    {
        $locationsString = $this->scopeConfig->getValue(
            self::XML_PATH_INSTORE_PICKUP_OPENINGS_2 . 'location',
            ScopeInterface::SCOPE_STORE
        );

        return !empty($locationsString) ? explode(',', $locationsString) : [];
    }

    /**
     * Retrieves the configured sending hours message.
     *
     * @return string
     */
    public function getSendingHoursMessageTwo()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_INSTORE_PICKUP_OPENINGS_2 . 'sending_hours',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieves the configured delivery time message.
     *
     * @return string
     */
    public function getDeliveryTimeMessageTwo()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_INSTORE_PICKUP_OPENINGS_2 . 'delivery_time_message',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieves the configured rest sending hours message.
     *
     * @return string
     */
    public function getRestSendingHoursMessageTwo()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_INSTORE_PICKUP_OPENINGS_2 . 'rest_sending_hours',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieves the configured rest delivery time message.
     *
     * @return string
     */
    public function getRestDeliveryTimeMessageTwo()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_INSTORE_PICKUP_OPENINGS_2 . 'rest_delivery_time_message',
            ScopeInterface::SCOPE_STORE
        );
    }
}

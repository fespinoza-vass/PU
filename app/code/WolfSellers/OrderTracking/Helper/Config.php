<?php
/**
 * Created by.
 *
 * User: Juan Carlos Hdez <juanhernandez@wolfsellers.com>
 * Date: 2022-04-27
 * Time: 14:57
 */

declare(strict_types=1);

namespace WolfSellers\OrderTracking\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Admin Config.
 */
class Config extends AbstractHelper
{
    private const XML_PATH = 'wolfsellers_tracking/%s/';
    private const STATUS_RECEIVED_FIELD = 'status_received';
    private const STATUS_READY_FIELD = 'status_ready';
    private const STATUS_IN_TRANSIT_FIELD = 'status_in_transit';
    private const STATUS_DELIVERED_FIELD = 'status_delivered';
    private const STATUS_UNDELIVERED_FIELD = 'status_undelivered';

    /**
     * Status received.
     *
     * @param string $carrierCode
     *
     * @return array
     */
    public function getStatusReceived(string $carrierCode): array
    {
        return $this->getStatusOptions(self::STATUS_RECEIVED_FIELD, $carrierCode);
    }

    /**
     * Status ready.
     *
     * @param string $carrierCode
     *
     * @return array
     */
    public function getStatusReady(string $carrierCode): array
    {
        return $this->getStatusOptions(self::STATUS_READY_FIELD, $carrierCode);
    }

    /**
     * Status in transit.
     *
     * @param string $carrierCode
     *
     * @return array
     */
    public function getStatusInTransit(string $carrierCode): array
    {
        return $this->getStatusOptions(self::STATUS_IN_TRANSIT_FIELD, $carrierCode);
    }

    /**
     * Status delivered.
     *
     * @param string $carrierCode
     *
     * @return array
     */
    public function getStatusDelivered(string $carrierCode): array
    {
        return $this->getStatusOptions(self::STATUS_DELIVERED_FIELD, $carrierCode);
    }

    /**
     * Status undelivered.
     *
     * @param string $carrierCode
     *
     * @return array
     */
    public function getStatusUndelivered(string $carrierCode): array
    {
        return $this->getStatusOptions(self::STATUS_UNDELIVERED_FIELD, $carrierCode);
    }

    /**
     * Status options.
     *
     * @param string $field
     * @param string $carrierCode
     *
     * @return array
     */
    private function getStatusOptions(string $field, string $carrierCode): array
    {
        $options = (string) $this->getValue($field, $carrierCode);

        return array_filter(array_map('trim', explode(',', $options)));
    }

    /**
     * Get config field value.
     *
     * @param string $field
     * @param string $carrierCode
     *
     * @return mixed
     */
    private function getValue(string $field, string $carrierCode)
    {
        $path = sprintf(self::XML_PATH, $carrierCode).$field;

        return $this->scopeConfig->getValue($path);
    }
}

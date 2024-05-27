<?php
/**
 * Created by.
 *
 * User: Juan Carlos Hdez <juanhernandez@wolfsellers.com>
 * Date: 2022-04-27
 * Time: 14:34
 */

declare(strict_types=1);

namespace WolfSellers\OrderTracking\Block;

use Magento\Framework\View\Element\Template;
use Magento\Shipping\Model\Order\Track;
use Magento\Shipping\Model\Tracking\Result\Status;
use WolfSellers\OrderTracking\Helper\Config;

/**
 * Tracking Status Block.
 */
class TrackingStatus extends Template
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'WolfSellers_OrderTracking::tracking/status.phtml';

    /** @var Track */
    private Track $shipmentTrack;

    /** @var Status */
    private Status $trackingInfo;

    /** @var Config */
    private Config $config;

    /**
     * @param Template\Context $context
     * @param Config $config
     * @param array $data
     */
    public function __construct(Template\Context $context, Config $config, array $data = [])
    {
        parent::__construct($context, $data);

        $this->config = $config;
    }

    /**
     * Set Shipment track.
     *
     * @param Track $shipmentTrack
     *
     * @return $this
     */
    public function setShipmentTrack(Track $shipmentTrack): TrackingStatus
    {
        $this->shipmentTrack = $shipmentTrack;

        return $this;
    }

    /**
     * Set tracking info.
     *
     * @param Status $trackingInfo
     *
     * @return $this
     */
    public function setTrackingInfo(Status $trackingInfo): TrackingStatus
    {
        $this->trackingInfo = $trackingInfo;

        return $this;
    }

    /**
     * Is order received.
     *
     * @return bool
     */
    public function isReceived(): bool
    {
        return $this->hasStatus('getStatusReceived');
    }

    /**
     * Is order ready.
     *
     * @return bool
     */
    public function isReady(): bool
    {
        return $this->hasStatus('getStatusReady');
    }

    /**
     * Is order in transit.
     *
     * @return bool
     */
    public function isInTransit(): bool
    {
        return $this->hasStatus('getStatusInTransit');
    }

    /**
     * Is order delivered.
     *
     * @return bool
     */
    public function isDelivered(): bool
    {
        return $this->hasStatus('getStatusDelivered');
    }

    /**
     * Is order undelivered.
     *
     * @return bool
     */
    public function isUndelivered(): bool
    {
        return $this->hasStatus('getStatusUndelivered');
    }

    /**
     * Check if it has status.
     *
     * @param string $statusMethod
     *
     * @return bool
     */
    private function hasStatus(string $statusMethod): bool
    {
        $status = $this->config->$statusMethod($this->shipmentTrack->getCarrierCode());

        if (!$status) {
            return false;
        }

        $carrierStatus = array_column($this->trackingInfo->getProgressdetail(), 'status_code');

        return (bool) array_intersect($status, $carrierStatus);
    }
}

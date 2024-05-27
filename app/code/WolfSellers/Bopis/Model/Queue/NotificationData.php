<?php


namespace WolfSellers\Bopis\Model\Queue;


use Magento\Framework\Data\AbstractDataObject;

class NotificationData extends AbstractDataObject
{
    const CANCELED_ORDER = "canceled";
    const HOLDED_ORDER = "holded";
    const NEW_ORDER = "new";

    private int $orderId;
    private string $type;

    /**
     * NotificationData constructor.
     * @param int $orderId
     * @param string $type
     */
    public function __construct(int $orderId = 0, string $type = self::NEW_ORDER)
    {
        $this->orderId = $orderId;
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getOrderId(): int
    {
        return $this->orderId;
    }

    /**
     * @param $orderId
     * @return NotificationData
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param $type
     * @return NotificationData
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }
}

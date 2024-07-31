<?php
/**
 * Copyright © Bopis All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace WolfSellers\Bopis\Model;

use Magento\Framework\Model\AbstractModel;
use WolfSellers\Bopis\Api\Data\NotificationInterface;

class Notification extends AbstractModel implements NotificationInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\WolfSellers\Bopis\Model\ResourceModel\Notification::class);
    }

    /**
     * @inheritDoc
     */
    public function getNotificationId()
    {
        return $this->getData(self::NOTIFICATION_ID);
    }

    /**
     * @inheritDoc
     */
    public function setNotificationId($notificationId)
    {
        return $this->setData(self::NOTIFICATION_ID, $notificationId);
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * @inheritDoc
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * @inheritDoc
     */
    public function getUserId()
    {
        return $this->getData(self::USER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setUserId($userId)
    {
        return $this->setData(self::USER_ID, $userId);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @inheritDoc
     */
    public function getRead()
    {
        return $this->getData(self::READ);
    }

    /**
     * @inheritDoc
     */
    public function setRead($read)
    {
        return $this->setData(self::READ, $read);
    }
}

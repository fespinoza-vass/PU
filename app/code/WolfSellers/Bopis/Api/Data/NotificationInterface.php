<?php
/**
 * Copyright © Bopis All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace WolfSellers\Bopis\Api\Data;

interface NotificationInterface
{

    const TYPE = 'type';
    const USER_ID = 'user_id';
    const NOTIFICATION_ID = 'notification_id';
    const ORDER_ID = 'order_id';
    const CREATED_AT = 'created_at';
    const READ = 'read';

    /**
     * Get notification_id
     * @return string|null
     */
    public function getNotificationId();

    /**
     * Set notification_id
     * @param string $notificationId
     * @return \WolfSellers\Bopis\Notification\Api\Data\NotificationInterface
     */
    public function setNotificationId($notificationId);

    /**
     * Get type
     * @return string|null
     */
    public function getType();

    /**
     * Set type
     * @param string $type
     * @return \WolfSellers\Bopis\Notification\Api\Data\NotificationInterface
     */
    public function setType($type);

    /**
     * Get type
     * @return string|null
     */
    public function getRead();

    /**
     * Set type
     * @param string $read
     * @return \WolfSellers\Bopis\Notification\Api\Data\NotificationInterface
     */
    public function setRead($read);

    /**
     * Get order_id
     * @return string|null
     */
    public function getOrderId();

    /**
     * Set order_id
     * @param string $orderId
     * @return \WolfSellers\Bopis\Notification\Api\Data\NotificationInterface
     */
    public function setOrderId($orderId);

    /**
     * Get user_id
     * @return string|null
     */
    public function getUserId();

    /**
     * Set user_id
     * @param string $userId
     * @return \WolfSellers\Bopis\Notification\Api\Data\NotificationInterface
     */
    public function setUserId($userId);

    /**
     * Get user_id
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set user_id
     * @param string $createdAt
     * @return \WolfSellers\Bopis\Notification\Api\Data\NotificationInterface
     */
    public function setCreatedAt($createdAt);
}

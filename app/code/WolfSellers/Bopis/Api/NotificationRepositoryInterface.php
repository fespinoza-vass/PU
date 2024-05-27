<?php
/**
 * Copyright © Bopis All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace WolfSellers\Bopis\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface NotificationRepositoryInterface
{

    /**
     * Save Notification
     * @param \WolfSellers\Bopis\Api\Data\NotificationInterface $notification
     * @return \WolfSellers\Bopis\Api\Data\NotificationInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \WolfSellers\Bopis\Api\Data\NotificationInterface $notification
    );

    /**
     * Retrieve Notification
     * @param string $notificationId
     * @return \WolfSellers\Bopis\Api\Data\NotificationInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($notificationId);

    /**
     * Retrieve Notification matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \WolfSellers\Bopis\Api\Data\NotificationSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * @param string $userId
     * @return \WolfSellers\Bopis\Model\ResourceModel\Notification\Collection
     */
    public function getListByUserId($userId);

    /**
     * Delete Notification
     * @param \WolfSellers\Bopis\Api\Data\NotificationInterface $notification
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \WolfSellers\Bopis\Api\Data\NotificationInterface $notification
    );

    /**
     * Delete Notification by ID
     * @param string $notificationId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($notificationId);
}

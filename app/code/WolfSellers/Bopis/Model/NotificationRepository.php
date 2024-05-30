<?php
/**
 * Copyright © Bopis All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace WolfSellers\Bopis\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use WolfSellers\Bopis\Api\Data\NotificationInterface;
use WolfSellers\Bopis\Api\Data\NotificationInterfaceFactory;
use WolfSellers\Bopis\Api\Data\NotificationSearchResultsInterfaceFactory;
use WolfSellers\Bopis\Api\NotificationRepositoryInterface;
use WolfSellers\Bopis\Model\ResourceModel\Notification as ResourceNotification;
use WolfSellers\Bopis\Model\ResourceModel\Notification\CollectionFactory as NotificationCollectionFactory;

class NotificationRepository implements NotificationRepositoryInterface
{

    /**
     * @var ResourceNotification
     */
    protected $resource;

    /**
     * @var Notification
     */
    protected $searchResultsFactory;

    /**
     * @var NotificationInterfaceFactory
     */
    protected $notificationFactory;

    /**
     * @var NotificationCollectionFactory
     */
    protected $notificationCollectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;


    /**
     * @param ResourceNotification $resource
     * @param NotificationInterfaceFactory $notificationFactory
     * @param NotificationCollectionFactory $notificationCollectionFactory
     * @param NotificationSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceNotification $resource,
        NotificationInterfaceFactory $notificationFactory,
        NotificationCollectionFactory $notificationCollectionFactory,
        NotificationSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->notificationFactory = $notificationFactory;
        $this->notificationCollectionFactory = $notificationCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(NotificationInterface $notification)
    {
        try {
            $this->resource->save($notification);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the notification: %1',
                $exception->getMessage()
            ));
        }
        return $notification;
    }

    /**
     * @inheritDoc
     */
    public function get($notificationId)
    {
        $notification = $this->notificationFactory->create();
        $this->resource->load($notification, $notificationId);
        if (!$notification->getId()) {
            throw new NoSuchEntityException(__('Notification with id "%1" does not exist.', $notificationId));
        }
        return $notification;
    }

    /**
     * @inheritDoc
     */
    public function getListByUserId($userId)
    {
        /** @var \WolfSellers\Bopis\Model\ResourceModel\Notification\Collection $collection */
        $collection = $this->notificationCollectionFactory->create();
        $collection->addFieldToFilter("user_id", $userId);
        return $collection;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->notificationCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model;
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(NotificationInterface $notification)
    {
        try {
            $notificationModel = $this->notificationFactory->create();
            $this->resource->load($notificationModel, $notification->getNotificationId());
            $this->resource->delete($notificationModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Notification: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($notificationId)
    {
        return $this->delete($this->get($notificationId));
    }
}

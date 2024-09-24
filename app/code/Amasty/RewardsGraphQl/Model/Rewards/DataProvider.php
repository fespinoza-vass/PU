<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Reward Points GraphQL (System)
 */

namespace Amasty\RewardsGraphQl\Model\Rewards;

use Amasty\Rewards\Api\RewardsRepositoryInterface;
use Amasty\Rewards\Model\Config\Source\Actions;
use Amasty\Rewards\Model\Date;
use Amasty\Rewards\Model\ResourceModel\Rewards\Collection;
use Amasty\Rewards\Model\ResourceModel\Rewards\CollectionFactory;

class DataProvider
{
    /**
     * @var RewardsRepositoryInterface
     */
    private $rewardsRepository;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Actions
     */
    private $actions;

    /**
     * @var Date
     */
    private $date;

    public function __construct(
        RewardsRepositoryInterface $rewardsRepository,
        CollectionFactory $collectionFactory,
        Actions $actions,
        Date $date
    ) {
        $this->rewardsRepository = $rewardsRepository;
        $this->collectionFactory = $collectionFactory;
        $this->actions = $actions;
        $this->date = $date;
    }

    /**
     * @param int $customerId
     *
     * @return float
     */
    public function getCustomerBalance(int $customerId): float
    {
        return (float)$this->rewardsRepository->getCustomerRewardBalance($customerId);
    }

    /**
     * @param int $customerId
     * @param int $limit
     * @param int $page
     *
     * @return \Amasty\Rewards\Model\Rewards[]
     */
    public function getHistoryItems(int $customerId, int $limit, int $page)
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addCustomerIdFilter($customerId)
            ->addExpiration($this->date->getDateWithOffsetByDays(0))
            ->setPageSize($limit)
            ->setCurPage($page);

        return $this->matchActions($collection->getItems());
    }

    /**
     * @param int $customerId
     *
     * @return int
     */
    public function getTotalHistoryRecordsCount(int $customerId): int
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addCustomerIdFilter($customerId);

        return $collection->getSize();
    }

    /**
     * @param \Amasty\Rewards\Model\Rewards[] $items
     * @return \Amasty\Rewards\Model\Rewards[]
     */
    private function matchActions($items)
    {
        $options = $this->actions->toOptionArray();
        /** @var \Amasty\Rewards\Model\Rewards $item */
        foreach ($items as $item) {
            if (array_key_exists($item->getAction(), $options)) {
                $item->setAction($options[$item->getAction()]);
            }
        }

        return $items;
    }
}

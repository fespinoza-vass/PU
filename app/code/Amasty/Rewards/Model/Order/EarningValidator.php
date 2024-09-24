<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Reward Points Base for Magento 2
 */

namespace Amasty\Rewards\Model\Order;

use Amasty\Rewards\Api\Data\SalesQuote\EntityInterface;
use Amasty\Rewards\Api\Data\SalesQuote\OrderInterface;
use Amasty\Rewards\Model\Config;
use Amasty\Rewards\Model\Repository\StatusHistoryRepository;
use Amasty\Rewards\Model\ResourceModel\StatusHistory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface as SalesOrderInterface;
use Magento\Sales\Model\Order as SalesOrder;

class EarningValidator
{
    /**
     * For earn rewards
     */
    public const ADD_COMMENT_ACTION = 'addComment';

    /**
     * @var Config
     */
    private $rewardsConfig;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var StatusHistoryRepository
     */
    private $statusHistoryRepository;

    public function __construct(
        Config $rewardsConfig,
        RequestInterface $request,
        StatusHistoryRepository $statusHistoryRepository
    ) {
        $this->rewardsConfig = $rewardsConfig;
        $this->request = $request;
        $this->statusHistoryRepository = $statusHistoryRepository;
    }

    /**
     * Return true if can earn rewards by MONEY_SPENT_ACTION or ORDER_COMPLETED_ACTION actions
     *
     * @param SalesOrder $orderModel
     * @return bool
     * @throws LocalizedException
     */
    public function canEarn(SalesOrderInterface $orderModel): bool
    {
        return $this->isOrderNotProcessed($orderModel)
            && $orderModel->getCustomerId()
            && $this->rewardsConfig->isEnabled($orderModel->getStoreId())
            && $this->checkOrderState($orderModel)
            && $this->isRequestValid()
            && $this->isPossibleEarningByCustomerStatus((int)$orderModel->getCustomerId(), $orderModel->getCreatedAt())
            && !($this->rewardsConfig->isDisabledEarningByRewards($orderModel->getStoreId())
                && $orderModel->getData(EntityInterface::POINTS_SPENT))
            && !$orderModel->getTotalRefunded();
    }

    public function checkOrderState(SalesOrderInterface $orderModel): bool
    {
        return $orderModel->getState() === SalesOrder::STATE_COMPLETE;
    }

    private function isPossibleEarningByCustomerStatus(int $customerId, string $creationDate): bool
    {
        if ($customerId) {
            $statusEntity = $this->statusHistoryRepository->getByCustomerIdAndDate($customerId, $creationDate);
            if ($statusEntity && $statusEntity->getAction() !== StatusHistory::EXCLUDE_ACTION) {
                return true;
            }
        }

        return false;
    }

    private function isRequestValid(): bool
    {
        return $this->request->getActionName() !== self::ADD_COMMENT_ACTION
            && !$this->request->getParam('creditmemo');
    }

    private function isOrderNotProcessed($orderModel): bool
    {
        return $orderModel->getData(EntityInterface::POINTS_EARN) === null
            && $orderModel->getData(OrderInterface::ORDER_PROCESSED_ATTRIBUTE)
            !== OrderInterface::ORDER_PROCESSED_STATUS;
    }
}

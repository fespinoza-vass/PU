<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Reward Points Base for Magento 2
 */

namespace Amasty\Rewards\Model\Order;

use Amasty\Rewards\Api\RewardsProviderInterface;
use Amasty\Rewards\Api\RewardsRepositoryInterface;
use Amasty\Rewards\Model\Config;
use Amasty\Rewards\Model\Config\Source\Actions;
use Amasty\Rewards\Model\Date;
use Magento\Framework\App\ObjectManager;
use Magento\Sales\Model\Order;

class CancelOrderProcessor
{
    /**
     * @var RewardsProviderInterface
     */
    private $rewardsProvider;

    /**
     * @var RewardsRepositoryInterface
     */
    private $rewardsRepository;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Date
     */
    private $date;

    public function __construct(
        RewardsProviderInterface $rewardsProvider,
        RewardsRepositoryInterface $rewardsRepository,
        Config $config = null, // TODO move to not optional
        Date $date = null // TODO move to not optional
    ) {
        $this->rewardsProvider = $rewardsProvider;
        $this->rewardsRepository = $rewardsRepository;
        $this->config = $config ?? ObjectManager::getInstance()->get(Config::class);
        $this->date = $date ?? ObjectManager::getInstance()->get(Date::class);
    }

    /**
     * @param Order $order
     * @param float $amount
     */
    public function refundUsedRewards(Order $order, float $amount): void
    {
        $modelRewards = $this->rewardsRepository->getEmptyModel();
        $modelRewards->setCustomerId((int)$order->getCustomerId());
        $modelRewards->setAmount($amount);
        if ($this->config->getExpirationBehavior($order->getStoreId())) {
            $expirationPeriod = $this->config->getExpirationPeriod($order->getStoreId());
            $expirationDate = $this->date->getDateWithOffsetByDays($expirationPeriod);
            $modelRewards->setExpirationDate($expirationDate);
            $modelRewards->setExpiringAmount($amount);
        }

        $modelRewards->setComment(__('Order #%1 Canceled', $order->getIncrementId())->render());
        $modelRewards->setAction(Actions::CANCEL_ACTION);
        $this->rewardsProvider->addRewardPoints($modelRewards, (int)$order->getStoreId());
    }
}

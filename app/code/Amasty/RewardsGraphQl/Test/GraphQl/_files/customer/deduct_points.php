<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Reward Points GraphQL (System)
 */

use Amasty\Rewards\Api\RewardsProviderInterface;
use Amasty\Rewards\Api\RewardsRepositoryInterface;
use Amasty\Rewards\Model\Config\Source\Actions;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var DataPersistorInterface $persistor */
$persistor = Bootstrap::getObjectManager()->get(DataPersistorInterface::class);

/** @var CustomerRepositoryInterface $customerRepository */
$customerRepository = Bootstrap::getObjectManager()->create(CustomerRepositoryInterface::class);

/** @var RewardsRepositoryInterface $rewardsRepository */
$rewardsRepository = $objectManager->create(RewardsRepositoryInterface::class);

/** @var RewardsProviderInterface $providerRewards */
$providerRewards = $objectManager->create(RewardsProviderInterface::class);

$customer = $customerRepository->get('rewardspoints@amasty.com');
$customerId = $customer->getId();

$rewardsModel = $rewardsRepository->getEmptyModel();
$rewardsModel->setCustomerId((int)$customerId);
$rewardsModel->setAmount(3.25);
$rewardsModel->setComment('comment');
$rewardsModel->setAction(Actions::ADMIN_ACTION);

$providerRewards->deductRewardPoints($rewardsModel);

$history = $rewardsRepository->getByCustomerId($customerId);
$historyId = 0;

foreach ($history as $k => $v) {
    if ($historyId < $v['id']) {
        $historyId = $v['id'];
    }
}

$persistor->set('rewards_deduct_points_id', $historyId);

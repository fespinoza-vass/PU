<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Reward Points GraphQL (System)
 */

use Amasty\Rewards\Api\RewardsProviderInterface;
use Amasty\Rewards\Model\Config\Source\Actions;
use Amasty\Rewards\Model\Repository\RewardsRepository;
use Amasty\Rewards\Model\RewardsProvider;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var DataPersistorInterface $persistor */
$persistor = Bootstrap::getObjectManager()->get(DataPersistorInterface::class);

/** @var CustomerRepositoryInterface $customerRepository */
$customerRepository = Bootstrap::getObjectManager()->create(CustomerRepositoryInterface::class);

/** @var RewardsRepository $rewardsRepository*/
$rewardsRepository = $objectManager->create(RewardsRepository::class);

/** @var RewardsProviderInterface $providerRewards */
$providerRewards = $objectManager->create(RewardsProviderInterface::class);

$customer = $customerRepository->get('rewardspoints@amasty.com');
$customerId = $customer->getId();

$rewardsModel = $rewardsRepository->getEmptyModel();
$rewardsModel->setCustomerId((int)$customerId);
$rewardsModel->setAmount(3);
$rewardsModel->setComment('order paid');
$rewardsModel->setAction(Actions::REWARDS_SPEND_ACTION);
$rewardsModel->setVisibleForCustomer(RewardsProvider::EVERYTIME_VISIBLE_FOR_CUSTOMER);
$providerRewards->deductRewardPoints($rewardsModel);

$history = $rewardsRepository->getByCustomerId($customerId);
$historyId = 0;

foreach ($history as $k => $v) {
    if ($historyId < $v['id']) {
        $historyId = $v['id'];
    }
}

$persistor->set('rewards_added_spend_id', $historyId);

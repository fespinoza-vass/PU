<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Reward Points GraphQL (System)
 */

use Amasty\Rewards\Api\RewardsRepositoryInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var DataPersistorInterface $persistor */
$persistor = Bootstrap::getObjectManager()->get(DataPersistorInterface::class);

/** @var RewardsRepositoryInterface $repository */
$repository = $objectManager->create(RewardsRepositoryInterface::class);

$addedId = $persistor->get('rewards_added_spend_id');
if ($addedId) {
    $repository->deleteById((int)$addedId);
}
$persistor->clear('rewards_added_spend_id');

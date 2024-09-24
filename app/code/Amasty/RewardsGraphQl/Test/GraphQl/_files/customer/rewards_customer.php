<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Reward Points GraphQL (System)
 */

use Amasty\Rewards\Model\ConstantRegistryInterface as Constant;
use Magento\Customer\Model\Customer;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var Customer $customer */
$customer = $objectManager->create(Customer::class);

$customer->setWebsiteId(1)
    ->setEmail('rewardspoints@amasty.com')
    ->setPassword('rewardspassword')
    ->setGroupId(1)
    ->setStoreId(1)
    ->setIsActive(1)
    ->setPrefix('Mr.')
    ->setFirstname('John')
    ->setMiddlename('A')
    ->setLastname('Smith')
    ->setSuffix('Esq.')
    ->setTaxvat('12')
    ->setGender(0);

$customer->isObjectNew(true);
$customer->save();

$customer->setAmrewardsEarningNotification(0);
$customer->setAmrewardsExpireNotification(1);

$customer->getResource()->saveAttribute($customer, Constant::NOTIFICATION_EARNING);
$customer->getResource()->saveAttribute($customer, Constant::NOTIFICATION_EXPIRE);

<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Reward Points GraphQL (System)
 */

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Registry;
use Magento\Integration\Model\Oauth\Token\RequestThrottler;
use Magento\TestFramework\Helper\Bootstrap;

/** @var Registry $registry */
$registry = Bootstrap::getObjectManager()->get(Registry::class);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var CustomerRepositoryInterface $customerRepository */
$customerRepository = Bootstrap::getObjectManager()->create(CustomerRepositoryInterface::class);

$customer = $customerRepository->get('rewardspoints@amasty.com');
$customerRepository->delete($customer);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);

/** @var RequestThrottler $throttler */
$throttler = Bootstrap::getObjectManager()->create(RequestThrottler::class);
$throttler->resetAuthenticationFailuresCount('rewardspoints@amasty.com', RequestThrottler::USER_TYPE_CUSTOMER);

<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Reward Points GraphQL (System)
 */

namespace Amasty\RewardsGraphQl\Plugin\CustomerGraphQl\Model\Customer\CreateCustomerAccount;

use Amasty\Rewards\Api\HistoryRepositoryInterface;
use Amasty\Rewards\Api\RewardsProviderInterface;
use Amasty\Rewards\Api\RuleRepositoryInterface;
use Amasty\Rewards\Model\Config;
use Amasty\Rewards\Model\Config\Source\Actions;
use Amasty\Rewards\Model\Customer\SubscribeToNotificationsByDefault;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\CustomerGraphQl\Model\Customer\CreateCustomerAccount;
use Magento\Store\Api\Data\StoreInterface;

/**
 * @see \Magento\CustomerGraphQl\Model\Customer\CreateCustomerAccount::execute()
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class ExecuteRegisterAction
{
    /**
     * @var HistoryRepositoryInterface
     */
    private $historyRepository;

    /**
     * @var RewardsProviderInterface
     */
    private $rewardsProvider;

    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var Config
     */
    private $configProvider;

    /**
     * @var SubscribeToNotificationsByDefault
     */
    private $subscribeToNotificationsByDefault;

    public function __construct(
        HistoryRepositoryInterface $historyRepository,
        RewardsProviderInterface $rewardsProvider,
        RuleRepositoryInterface $ruleRepository,
        Config $configProvider,
        SubscribeToNotificationsByDefault $subscribeToNotificationsByDefault
    ) {
        $this->historyRepository = $historyRepository;
        $this->rewardsProvider = $rewardsProvider;
        $this->ruleRepository = $ruleRepository;
        $this->configProvider = $configProvider;
        $this->subscribeToNotificationsByDefault = $subscribeToNotificationsByDefault;
    }

    public function afterExecute(
        CreateCustomerAccount $subject,
        CustomerInterface $customer,
        array $data,
        StoreInterface $store
    ): CustomerInterface {
        if (!$this->configProvider->isEnabled()) {
            return $customer;
        }

        /** @var int[] $appliedActions */
        $appliedActions = $this->historyRepository->getAppliedActionsId($customer->getId());

        $rules = $this->ruleRepository->getRulesByAction(
            Actions::REGISTRATION_ACTION,
            $customer->getWebsiteId(),
            GroupInterface::NOT_LOGGED_IN_ID
        );

        /** @var \Amasty\Rewards\Model\Rule $rule */
        foreach ($rules as $rule) {
            if (!isset($appliedActions[$rule->getId()])) {
                $this->rewardsProvider->addPointsByRule($rule, $customer->getId(), $customer->getStoreId());
            }
        }

        $this->subscribeToNotificationsByDefault->execute($customer);

        return $customer;
    }
}

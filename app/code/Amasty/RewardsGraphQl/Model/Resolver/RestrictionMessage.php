<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Reward Points GraphQL (System)
 */

namespace Amasty\RewardsGraphQl\Model\Resolver;

use Amasty\Rewards\Model\Config;
use Amasty\Rewards\Model\Quote\EarningChecker;
use Magento\CustomerGraphQl\Model\Customer\GetCustomer;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class RestrictionMessage implements ResolverInterface
{
    /**
     * @var GetCustomer
     */
    private $customerGetter;

    /**
     * @var EarningChecker
     */
    private $earningChecker;

    /**
     * @var Config
     */
    private $configProvider;

    public function __construct(
        GetCustomer $customerGetter,
        EarningChecker $earningChecker,
        Config $configProvider
    ) {
        $this->customerGetter = $customerGetter;
        $this->earningChecker = $earningChecker;
        $this->configProvider = $configProvider;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        try {
            $customer = $this->customerGetter->execute($context);
        } catch (\Exception $e) {
            unset($e);
            return false;
        }

        return $this->earningChecker->isForbiddenEarningByCustomerStatus((int)$customer->getId())
            && $this->configProvider->isRestrictionMessageEnabled()
            && !empty($this->getMessageText());
    }

    public function getMessageText(): ?string
    {
        return $this->configProvider->getRestrictionMessageText();
    }
}

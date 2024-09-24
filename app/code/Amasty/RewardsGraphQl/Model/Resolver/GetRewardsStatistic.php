<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Reward Points GraphQL (System)
 */

namespace Amasty\RewardsGraphQl\Model\Resolver;

use Amasty\Rewards\Model\ResourceModel\Rewards;
use Magento\CustomerGraphQl\Model\Customer\GetCustomer;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class GetRewardsStatistic implements ResolverInterface
{
    /**
     * @var GetCustomer
     */
    private $customerGetter;

    /**
     * @var Rewards
     */
    private $rewardsResource;

    public function __construct(
        GetCustomer $customerGetter,
        Rewards $rewardsResource
    ) {
        $this->customerGetter = $customerGetter;
        $this->rewardsResource = $rewardsResource;
    }

    /**
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array|\Magento\Framework\GraphQl\Query\Resolver\Value|mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $customer = $this->customerGetter->execute($context);

        return $this->rewardsResource->getStatistic((int)$customer->getId());
    }
}

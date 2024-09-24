<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Reward Points GraphQL (System)
 */

namespace Amasty\RewardsGraphQl\Model\Resolver;

use Amasty\Rewards\Model\ConstantRegistryInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\ResourceModel\Customer as CustomerResource;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class GetCustomerNotificationOptions implements ResolverInterface
{
    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * @var CustomerResource
     */
    private $customerResource;

    public function __construct(
        CustomerFactory $customerFactory,
        CustomerResource $customerResource
    ) {
        $this->customerFactory = $customerFactory;
        $this->customerResource = $customerResource;
    }

    /**
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $customer = $this->customerFactory->create();
        $this->customerResource->load($customer, $context->getUserId());

        return [
            ConstantRegistryInterface::NOTIFICATION_EARNING => $customer->getAmrewardsEarningNotification(),
            ConstantRegistryInterface::NOTIFICATION_EXPIRE => $customer->getAmrewardsExpireNotification()
        ];
    }
}

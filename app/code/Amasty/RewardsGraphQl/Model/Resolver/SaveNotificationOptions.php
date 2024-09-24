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

class SaveNotificationOptions implements ResolverInterface
{
    public const EARN_OPTION = 'earn_option';
    public const EXPIRE_OPTION = 'expire_option';

    public const RESPONSE = 'response';

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
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $earnOption = $args['input'][self::EARN_OPTION] ?? false;
        $expireOption = $args['input'][self::EXPIRE_OPTION] ?? false;

        $customerModel = $this->customerFactory->create();
        $this->customerResource->load($customerModel, $context->getUserId());

        $customerModel->setData(ConstantRegistryInterface::NOTIFICATION_EARNING, $earnOption);
        $this->customerResource->saveAttribute($customerModel, ConstantRegistryInterface::NOTIFICATION_EARNING);

        $customerModel->setData(ConstantRegistryInterface::NOTIFICATION_EXPIRE, $expireOption);
        $this->customerResource->saveAttribute($customerModel, ConstantRegistryInterface::NOTIFICATION_EXPIRE);

        return [
            self::RESPONSE => __('Notification options were changed.'),
        ];
    }
}

<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Reward Points GraphQL (System)
 */

namespace Amasty\RewardsGraphQl\Model\Resolver;

use Amasty\RewardsGraphQl\Model\Rewards\DataProvider;
use Magento\CustomerGraphQl\Model\Customer\GetCustomer;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class HistoryItems implements ResolverInterface
{
    /**
     * @var GetCustomer
     */
    private $customerGetter;

    /**
     * @var DataProvider
     */
    private $dataProvider;

    public function __construct(
        GetCustomer $customerGetter,
        DataProvider $dataProvider
    ) {
        $this->customerGetter = $customerGetter;
        $this->dataProvider = $dataProvider;
    }

    /**
     * Fetches the data from persistence models and format it according to the GraphQL schema.
     *
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $customer = $this->customerGetter->execute($context);

        return $this->dataProvider->getHistoryItems((int)$customer->getId(), $args['pageSize'], $args['currentPage']);
    }
}

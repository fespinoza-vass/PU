<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Reward Points GraphQL (System)
 */

namespace Amasty\RewardsGraphQl\Model\Resolver;

use Amasty\RewardsGraphQl\Model\RequestProcessor;
use Amasty\RewardsGraphQl\Model\Rewards\DataProvider;
use Magento\CustomerGraphQl\Model\Customer\GetCustomer;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class Rewards implements ResolverInterface
{
    /**
     * @var GetCustomer
     */
    private $customerGetter;

    /**
     * @var DataProvider
     */
    private $dataProvider;

    /**
     * @var RequestProcessor
     */
    private $requestProcessor;

    public function __construct(
        GetCustomer $customerGetter,
        DataProvider $dataProvider,
        RequestProcessor $requestProcessor
    ) {
        $this->customerGetter = $customerGetter;
        $this->dataProvider = $dataProvider;
        $this->requestProcessor = $requestProcessor;
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
        $customerId = (int)$customer->getId();

        return [
            'balance' => $this->requestProcessor->isFieldRequested('balance', $info)
                ? $this->dataProvider->getCustomerBalance($customerId) : 0,
            'history' => [
                'total_count' => $this->requestProcessor->isFieldRequested('history/total_count', $info)
                    ? $this->dataProvider->getTotalHistoryRecordsCount($customerId) : 0,
            ],
            'highlight' => []
        ];
    }
}

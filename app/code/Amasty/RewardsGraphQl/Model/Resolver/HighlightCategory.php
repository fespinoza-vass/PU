<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Reward Points GraphQL (System)
 */

namespace Amasty\RewardsGraphQl\Model\Resolver;

use Amasty\Rewards\Api\CatalogHighlightManagementInterface;
use Amasty\Rewards\Api\Data\HighlightInterface;
use Magento\CustomerGraphQl\Model\Customer\GetCustomer;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class HighlightCategory implements ResolverInterface
{
    /**
     * @var GetCustomer
     */
    private $customerGetter;

    /**
     * @var CatalogHighlightManagementInterface
     */
    private $highlightManagement;

    public function __construct(
        GetCustomer $customerGetter,
        CatalogHighlightManagementInterface $highlightManagement
    ) {
        $this->customerGetter = $customerGetter;
        $this->highlightManagement = $highlightManagement;
    }

    /**
     * Fetches the data from persistence models and format it according to the GraphQL schema.
     *
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return HighlightInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $customer = $this->customerGetter->execute($context);
        $attributes = isset($args['attributes']) ? $args['attributes'] : null;

        return $this->highlightManagement->getHighlightForCategory(
            $args['productId'],
            (int)$customer->getId(),
            $attributes
        );
    }
}

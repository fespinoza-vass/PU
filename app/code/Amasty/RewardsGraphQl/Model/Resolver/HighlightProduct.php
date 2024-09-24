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
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class HighlightProduct implements ResolverInterface
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
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return HighlightInterface|\Magento\Framework\GraphQl\Query\Resolver\Value|mixed|null
     * @throws GraphQlInputException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!isset($value['model'])) {
            throw new GraphQlInputException(__('"model" value must be specified'));
        }

        /** @var \Magento\Catalog\Model\Product $product */
        $product = $value['model'];
        try {
            $customer = $this->customerGetter->execute($context);
        } catch (\Exception $e) {
            unset($e);
            return null;
        }

        // Request data
        $attributes = $args['attributes'] ?? null;

        return $this->highlightManagement->getHighlightForProduct(
            $product->getId(),
            (int)$customer->getId(),
            $attributes
        );
    }
}

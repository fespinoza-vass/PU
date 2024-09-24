<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Reward Points GraphQL (System)
 */

namespace Amasty\RewardsGraphQl\Model\Resolver;

use Amasty\Rewards\Api\Data\HighlightInterface;
use Amasty\Rewards\Api\GuestHighlightManagementInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class GuestHighlightCheckout implements ResolverInterface
{
    /**
     * @var GuestHighlightManagementInterface
     */
    private $guestHighlightManagement;

    public function __construct(GuestHighlightManagementInterface $guestHighlightManagement)
    {
        $this->guestHighlightManagement = $guestHighlightManagement;
    }

    /**
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return HighlightInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        return $this->guestHighlightManagement->getHighlight(GuestHighlightManagementInterface::PAGE_CHECKOUT);
    }
}

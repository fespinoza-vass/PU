<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Reward Points GraphQL (System)
 */

namespace Amasty\RewardsGraphQl\Model\Resolver;

use Amasty\Rewards\Model\RewardsPropertyProvider;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class GetRewardsProperty implements ResolverInterface
{
    public const POINTS_RATE = 'points_rate';
    public const CURRENCY = 'current_currency_code';

    /**
     * @var RewardsPropertyProvider
     */
    private $propertyProvider;

    public function __construct(RewardsPropertyProvider $propertyProvider)
    {
        $this->propertyProvider = $propertyProvider;
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
        return [
            self::POINTS_RATE => $this->propertyProvider->getCurrencyPointsRate(),
            self::CURRENCY => $context->getExtensionAttributes()->getStore()->getCurrentCurrency()->getCurrencyCode()
        ];
    }
}

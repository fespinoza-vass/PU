<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Reward Points GraphQL (System)
 */

namespace Amasty\RewardsGraphQl\Model\Resolver;

use Amasty\RewardsGraphQl\Model\Rewards\QuoteApplier;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\QuoteGraphQl\Model\Cart\GetCartForUser;

class UseRewardPoints implements ResolverInterface
{
    public const CART_ID_KEY = 'cart_id';
    public const POINTS_KEY = 'points';

    /**
     * @var QuoteApplier
     */
    private $applier;

    /**
     * @var GetCartForUser
     */
    private $getCartForUser;

    public function __construct(
        QuoteApplier $applier,
        GetCartForUser $getCartForUser
    ) {
        $this->applier = $applier;
        $this->getCartForUser = $getCartForUser;
    }

    /**
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     * @throws GraphQlInputException
     * @throws \GraphQL\Error\Error
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException
     * @throws \Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (empty($args['input'][self::CART_ID_KEY])) {
            throw new GraphQlInputException(__('Required parameter "%1" is missing', self::CART_ID_KEY));
        }

        if (!isset($args['input'][self::POINTS_KEY])) {
            throw new GraphQlInputException(__('Required parameter "%1" is missing', self::POINTS_KEY));
        }

        $cart = $this->getCartForUser->execute(
            $args['input'][self::CART_ID_KEY],
            $context->getUserId(),
            (int)$context->getExtensionAttributes()->getStore()->getId()
        );

        $result = $this->applier->apply((int)$cart->getId(), $args['input'][self::POINTS_KEY]);

        return [
            'cart' => [
                'model' => $cart,
            ],
            'response' => $result
        ];
    }
}

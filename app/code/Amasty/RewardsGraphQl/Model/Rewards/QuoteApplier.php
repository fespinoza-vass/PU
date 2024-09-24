<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Reward Points GraphQL (System)
 */

namespace Amasty\RewardsGraphQl\Model\Rewards;

use Amasty\Rewards\Api\CheckoutRewardsManagementInterface;
use GraphQL\Error\Error;
use Magento\Framework\Exception\LocalizedException;

class QuoteApplier
{
    /**
     * @var CheckoutRewardsManagementInterface
     */
    private $management;

    public function __construct(
        CheckoutRewardsManagementInterface $management
    ) {
        $this->management = $management;
    }

    /**
     * @param int $cartId
     * @param float $amount
     *
     * @return string
     *
     * @throws Error
     */
    public function apply(int $cartId, float $amount)
    {
        try {
            if ($amount) {
                $pointsData = $this->management->set($cartId, $amount);
                $result = $pointsData['notice'];
            } else {
                $this->management->remove($cartId);
                $result = __('Removed.');
            }
        } catch (LocalizedException $exception) {
            $result = $exception->getMessage();
        } catch (\Exception $exception) {
            throw new Error(__('Can not perform operation.'));
        }

        return $result;
    }
}

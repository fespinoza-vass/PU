<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Reward Points Base for Magento 2
 */

namespace Amasty\Rewards\Model\Quote\Collector;

use Amasty\Rewards\Api\Data\SalesQuote\EntityInterface;
use Amasty\Rewards\Model\Calculation\Discount;
use Amasty\Rewards\Model\Config;
use Amasty\Rewards\Model\Config\Source\RedemptionLimitTypes;
use Amasty\Rewards\Model\DiscountDescription;
use Magento\Framework\App\ObjectManager;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Magento\SalesRule\Model\Validator;
use Magento\Store\Model\StoreManagerInterface;

class Points extends AbstractTotal
{
    /**
     * @var Validator
     */
    private $validator;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Discount
     */
    private $calculator;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var DiscountDescription
     */
    private $discountDescription;

    public function __construct(
        Validator $validator,
        Config $config,
        Discount $calculator,
        StoreManagerInterface $storeManager,
        DiscountDescription $discountDescription = null //todo: move to not optional
    ) {
        $this->validator = $validator;
        $this->config = $config;
        $this->calculator = $calculator;
        $this->storeManager = $storeManager;
        $this->discountDescription = $discountDescription
            ?? ObjectManager::getInstance()->get(DiscountDescription::class);
    }

    /**
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     * @return $this
     */
    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ): self {
        $storeId = $quote->getStoreId();

        if (!$this->config->isEnabled($storeId)) {
            return $this;
        }
        /** @var $address Address */
        $address = $shippingAssignment->getShipping()->getAddress();
        $items = $shippingAssignment->getItems();
        $spentPoints = (float)$quote->getData(EntityInterface::POINTS_SPENT);

        if (!$items) {
            return $this;
        }

        if (!$spentPoints) {
            $this->calculator->clearPointsDiscount($items);

            return $this;
        }

        $isEnableLimit = $this->config->isEnableLimit($storeId);

        if ((int)$isEnableLimit === RedemptionLimitTypes::LIMIT_PERCENT) {
            $limitPercent = $this->config->getRewardPercentLimit($storeId);
            $useSubtotalInclTax = $this->config->isUseSubtotalInclTax($storeId);
            $subtotal = $useSubtotalInclTax ? $total->getSubtotalInclTax() : $total->getSubtotal();
            $rate = $this->config->getPointsRate($storeId);
            $basePoints = $spentPoints / $rate;
            $allowedPercent = round(($subtotal / 100 * $limitPercent) / $quote->getBaseToQuoteRate(), 2);

            if ($basePoints > $allowedPercent) {
                $spentPoints = $allowedPercent * $rate;
            }
        }

        $appliedPoints = $this->calculator->calculateDiscount($items, $total, $spentPoints);

        if (!$appliedPoints) {
            $this->calculator->clearPointsDiscount($items);
        }

        $this->discountDescription->addRewardsDescription($address, $appliedPoints);
        $quote->setData(EntityInterface::POINTS_SPENT, $this->getQuoteAppliedPoints($items));

        $total->setDiscountDescription($address->getDiscountDescription());
        $total->setSubtotalWithDiscount($total->getSubtotal() + $total->getDiscountAmount());
        $total->setBaseSubtotalWithDiscount($total->getBaseSubtotal() + $total->getBaseDiscountAmount());

        return $this;
    }

    /**
     * @param CartItemInterface[] $items
     * @return float
     */
    private function getQuoteAppliedPoints(array $items): float
    {
        $points = 0;

        foreach ($items as $item) {
            $points += $item->getData(EntityInterface::POINTS_SPENT);
        }
        $storeId = (int)$this->storeManager->getStore()->getId();
        $roundRule = $this->config->getRoundRule($storeId);
        if ($roundRule === 'up') {
            $points = ceil($points);
        }

        return (float)$points;
    }
}

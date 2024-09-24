<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Reward Points Base for Magento 2
 */

namespace Amasty\Rewards\Model\Calculation\Earning;

use Amasty\Rewards\Model\Calculation\ItemAmountCalculatorInterface;
use Amasty\Rewards\Model\Config;
use Amasty\Rewards\Model\Rule;
use Magento\Framework\App\ObjectManager;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\Sales\Model\Order\Item as OrderItem;
use Magento\Tax\Model\Config as TaxConfig;
use Magento\Weee\Model\Config as WeeeConfig;

class ItemAmountCalculator implements ItemAmountCalculatorInterface
{
    /**
     * @var Config
     */
    private $rewardsConfig;

    /**
     * @var WeeeConfig
     */
    private $configWee;

    /**
     * @var ModifierInterface[]
     */
    private $modifiers;

    /**
     * @var TaxConfig
     */
    private $taxConfig;

    public function __construct(
        Config $rewardsConfig,
        WeeeConfig $configWee,
        array $modifiers = [],
        TaxConfig $taxConfig = null
    ) {
        $this->rewardsConfig = $rewardsConfig;
        $this->configWee = $configWee;
        $this->modifiers = $modifiers;
        $this->taxConfig = $taxConfig ?? ObjectManager::getInstance()->get(TaxConfig::class);
    }

    /**
     * @param QuoteItem|OrderItem $item
     * @return float
     */
    public function calculateItemAmount($item): float
    {
        $calculationMode = $this->rewardsConfig->getEarningCalculationMode();

        $itemAmount = $item->getBaseRowTotal()
            - $item->getBaseDiscountAmount()
            + $item->getBaseDiscountTaxCompensationAmount();

        if ($calculationMode === Rule::AFTER_TAX && !$this->isTaxIncluded($item)) {
            $itemAmount += $item->getBaseTaxAmount();
            if ($this->configWee->isEnabled()) {
                $itemAmount += $item->getBaseWeeeTaxAppliedRowAmnt();
            }
        }

        return $this->modifyItemAmount($item, $itemAmount);
    }

    /**
     * @param QuoteItem|OrderItem $item
     * @param float $calculatedAmount
     * @return float
     */
    private function modifyItemAmount($item, float $calculatedAmount): float
    {
        foreach ($this->modifiers as $modifier) {
            if ($modifier instanceof ModifierInterface) {
                $calculatedAmount = $modifier->modifyItemAmount($item, $calculatedAmount);
            } else {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Modifier should implement %s interface.',
                        ModifierInterface::class
                    )
                );
            }
        }

        return (float)max(0, $calculatedAmount);
    }

    /**
     * @param QuoteItem|OrderItem $item
     * @return bool
     */
    private function isTaxIncluded($item): bool
    {
        return $this->taxConfig->discountTax()
            && !$this->taxConfig->priceIncludesTax()
            && $item->getBaseDiscountAmount() > 0
            && !empty($item->getAppliedRuleIds());
    }
}

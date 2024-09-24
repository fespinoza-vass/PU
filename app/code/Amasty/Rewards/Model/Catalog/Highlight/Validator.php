<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Reward Points Base for Magento 2
 */

namespace Amasty\Rewards\Model\Catalog\Highlight;

use Amasty\Conditions\Model\Rule\Condition\Product;
use Magento\Customer\Model\Customer;
use Magento\Framework\App\ObjectManager;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteFactory;

class Validator
{
    public const ALLOWED_CUSTOMER_CONDITIONS = [
        \Amasty\Conditions\Model\Rule\Condition\CustomerAttributes::class
    ];

    public const ALLOWED_PRODUCT_CONDITIONS = [
        \Magento\SalesRule\Model\Rule\Condition\Product\Found::class,
    ];

    public const ALLOWED_QUOTE_CONDITIONS = [
        Product::class,
    ];

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    private $quoteFactory;

    public function __construct(
        QuoteFactory $quoteFactory = null
    ) {
        $this->quoteFactory = $quoteFactory ?? ObjectManager::getInstance()->get(QuoteFactory::class);
    }

    /**
     * @param \Amasty\Rewards\Api\Data\RuleInterface $rule
     *
     * @param ValidObject $validObject
     *
     * @return bool
     */
    public function validate($rule, $validObject)
    {
        $conditions = $rule->getConditions()->getConditions();
        $all = $rule->getConditions()->getAggregator() === 'all';
        $true = (bool)$rule->getConditions()->getValue();

        /** @var \Magento\Rule\Model\Condition\AbstractCondition $condition */
        foreach ($conditions as $condition) {
            if ($entity = $this->getEntityByCondition($condition->getType(), $validObject)) {
                $validated = $condition->validate($entity);

                if ($all && $validated !== $true) {
                    return false;
                } elseif (!$all && $validated === $true) {
                    return true;
                }
            }
        }

        return $all ? true : false;
    }

    /**
     * Return entity for validation due to condition type
     *
     * @param string $conditionType
     * @param ValidObject $validObject
     *
     * @return ValidObject|Customer|bool|Quote
     */
    private function getEntityByCondition($conditionType, $validObject)
    {
        if (in_array($conditionType, self::ALLOWED_CUSTOMER_CONDITIONS)) {
            return $validObject->getCustomer();
        }

        if (in_array($conditionType, self::ALLOWED_PRODUCT_CONDITIONS)) {
            return $validObject;
        }

        if (in_array($conditionType, self::ALLOWED_QUOTE_CONDITIONS)) {
            return $this->getQuoteModelByValidObject($validObject);
        }

        return false;
    }

    private function getQuoteModelByValidObject(ValidObject $validObject): Quote
    {
        $quote = $this->quoteFactory->create();
        $quote->addProduct($validObject->getProduct());

        return $quote;
    }
}

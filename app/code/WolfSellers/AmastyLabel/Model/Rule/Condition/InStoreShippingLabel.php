<?php

declare(strict_types=1);

namespace WolfSellers\AmastyLabel\Model\Rule\Condition;

use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Amasty\Label\Model\Source\Rules\Operator\BooleanOptions as IsNewOptionSource;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Catalog\Model\Product;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Phrase;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\Context;
use WolfSellers\AmastyLabel\Helper\DynamicTagRules;

class InStoreShippingLabel extends AbstractCondition
{
    /**
     * @var Yesno
     */
    private $yesNoOptionProvider;

    /**
     * @var IsNewOptionSource
     */
    private $isNewOperatorProvider;

    /**
     * @var DynamicTagRules
     */
    private DynamicTagRules $dynamicTagRules;

    /**
     * @param Context $context
     * @param Yesno $yesNoOptionProvider
     * @param IsNewOptionSource $isNewOperatorProvider
     * @param DynamicTagRules $dynamicTagRules
     * @param array $data
     */
    public function __construct(
        Context           $context,
        Yesno             $yesNoOptionProvider,
        IsNewOptionSource $isNewOperatorProvider,
        DynamicTagRules   $dynamicTagRules,
        array             $data = []
    )
    {
        $this->yesNoOptionProvider = $yesNoOptionProvider;
        $this->isNewOperatorProvider = $isNewOperatorProvider;
        $this->dynamicTagRules = $dynamicTagRules;
        parent::__construct(
            $context,
            $data
        );
    }


    /**
     * @param ProductCollection $collection
     * @return void
     */
    public function collectValidatedAttributes(ProductCollection $collection): void
    {
        $collection->addAttributeToSelect('sku');
    }

    /**
     * @param AbstractModel $model
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function validate(AbstractModel $model): bool
    {
        /** @var Product $model * */
        $result = $this->dynamicTagRules->shippingLabelsByProductSku($model->getSku());
        $fastShippingLabelAvailable = isset($result['instore']) ? $result['instore'] : false;
        $requiredValue = (bool)$this->getValue();

        return $this->getOperator() === '==' ?
            ($fastShippingLabelAvailable === $requiredValue) :
            ($fastShippingLabelAvailable !== $requiredValue);
    }

    /**
     * @return Phrase
     */
    public function getAttributeElementHtml(): Phrase
    {
        return __('In-Store Label Rules');
    }

    /**
     * @return string
     */
    public function getInputType(): string
    {
        return 'select';
    }

    /**
     * @return string
     */
    public function getValueElementType(): string
    {
        return 'select';
    }

    /**
     * @return array
     */
    public function getOperatorSelectOptions(): array
    {
        return $this->isNewOperatorProvider->toOptionArray();
    }

    /**
     * @return array
     */
    public function getValueSelectOptions(): array
    {
        return $this->yesNoOptionProvider->toOptionArray();
    }
}

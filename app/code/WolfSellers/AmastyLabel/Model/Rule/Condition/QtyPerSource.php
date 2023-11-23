<?php

declare(strict_types=1);

namespace WolfSellers\AmastyLabel\Model\Rule\Condition;

use Amasty\Label\Model\Source\Rules\Operator\Qty as QtyOptionSource;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Phrase;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\Context;
use Magento\InventoryApi\Api\GetSourceItemsBySkuInterface;
use WolfSellers\Bopis\Model\ResourceModel\AbstractBopisCollection;
use WolfSellers\InventoryReserationBySource\Helper\InventoryBySourceHelper;

class QtyPerSource extends AbstractCondition
{

    /** @var InventoryBySourceHelper */
    protected $_inventoryBySource;
    /**
     * @var QtyOptionSource
     */
    private $qtyOptionSource;

    /**
     * @var GetSourceItemsBySkuInterface
     */
    private $sourceItemsBySku;

    /**
     * @param Context $context
     * @param QtyOptionSource $qtyOptionSource
     * @param GetSourceItemsBySkuInterface $sourceItemsBySku
     * @param array $data
     */
    public function __construct(
        InventoryBySourceHelper      $inventoryBySourceHelper,
        Context                      $context,
        QtyOptionSource              $qtyOptionSource,
        GetSourceItemsBySkuInterface $sourceItemsBySku,
        array                        $data = []
    )
    {
        $this->_inventoryBySource = $inventoryBySourceHelper;
        $this->qtyOptionSource = $qtyOptionSource;
        $this->sourceItemsBySku = $sourceItemsBySku;
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
     */
    public function validate(AbstractModel $model): bool
    {
        /** @var Product $model * */
        $maxQty = $this->getMaxQtyPerSource($model);
        return $this->validateAttribute($maxQty);
    }

    /**
     * @param Product $product
     * @return int
     */
    private function getMaxQtyPerSource(Product $product): int
    {
        $max = 0;

        $inventory = $this->sourceItemsBySku->execute($product->getSku());

        foreach ($inventory as $source) {
            if ($source->getSourceCode() == AbstractBopisCollection::DEFAULT_BOPIS_SOURCE_CODE) continue;

            if (!$source->getStatus()) continue;

            $sourceQuantity = $this->_inventoryBySource->getSalableQtyBySource($product->getSku(), $source->getSourceCode());

            if ($sourceQuantity > $max) {
                $max = $sourceQuantity;
            }
        }

        return (int)$max;
    }

    /**
     * @return Phrase
     */
    public function getAttributeElementHtml(): Phrase
    {
        return __('Qty per Source');
    }

    /**
     * @return string
     */
    public function getInputType(): string
    {
        return 'string';
    }

    /**
     * @return string
     */
    public function getValueElementType(): string
    {
        return 'text';
    }

    /**
     * @return array
     */
    public function getOperatorSelectOptions(): array
    {
        return $this->qtyOptionSource->toOptionArray();
    }
}

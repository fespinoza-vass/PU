<?php

namespace WolfSellers\AmastyLabel\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventoryApi\Api\GetSourceItemsBySkuInterface;
use WolfSellers\Bopis\Model\ResourceModel\AbstractBopisCollection;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\Product;
use WolfSellers\InventoryReservationBySource\Helper\InventoryBySourceHelper;

class DynamicTagRules extends AbstractHelper
{
    /** @var int */
    const GENERAL_MIN_STOCK = 2;

    /** @var string */
    const SOURCE_CODE_JOCKEY = '104';

    /** @var string */
    const SOURCE_CODE_CARACOL = '102';

    /** @var InventoryBySourceHelper */
    protected $_inventoryBySourceHelper;

    /**
     * @param Context $context
     * @param GetSourceItemsBySkuInterface $sourceItemsBySku
     * @param ProductRepository $productRepository
     * @param InventoryBySourceHelper $inventoryBySourceHelper
     */
    public function __construct(
        Context                                $context,
        protected GetSourceItemsBySkuInterface $sourceItemsBySku,
        protected ProductRepository            $productRepository,
        InventoryBySourceHelper                $inventoryBySourceHelper
    )
    {
        $this->_inventoryBySourceHelper = $inventoryBySourceHelper;
        parent::__construct($context);
    }

    /**
     * Get shipping labels available by sku
     * @param $sku
     * @return array
     * @throws NoSuchEntityException
     */
    public function shippingLabelsByProductSku($sku): array
    {
        $qty = $this->getMaxQtyPerSource($sku);

        return [
            'fast' => $this->fastShippingLabel($qty, $sku),
            'instore' => $this->InStoreLabel($qty, $sku)
        ];
    }

    /**
     * Returns true if the product has the fast shipping label.
     *
     * @param $qty
     * @param $sku
     * @return bool
     * @todo Set the configuration to get the GENERAL_MIN_STOCK value from the administrator.
     */
    public function fastShippingLabel($qty, $sku): bool
    {
        $labelAvailable = false;

        // Rule 1: If there is stock only in Lurín, the label will not be available.
        if ($qty['sources'] <= 0) {
            return false;
        }

        // EXCEPTIONS TO FAST SHIPPING LABEL RULES.
        //  If there is stock only in JockeyPlaza, only the InStore label is shown
        if ($this->isOnlyStockInJockey($qty)) {
            return false;
        }

        //  If there is stock only in Caracol, only the fast label is shown
        if ($this->isOnlyStockInCaracol($qty)) {
            return true;
        }

        // Label available if at least one source has stock.
        if ($qty['sources'] >= self::GENERAL_MIN_STOCK) {
            $labelAvailable = true;
        }

        // GENERAL RULES FOR LURIN
        /*if ($this->generalRules($sku, $qty)) {
            $labelAvailable = true;
        }*/

        return $labelAvailable;
    }

    /**
     * Returns true if the product has the in-store label.
     *
     * @param $qty
     * @param $sku
     * @return bool
     * @todo Set the configuration to get the GENERAL_MIN_STOCK value from the administrator.
     */
    public function InStoreLabel($qty, $sku): bool
    {
        $labelAvailable = false;

        // Rule 1: If there is stock only in Lurín, the label will not be available.
        if ($qty['sources'] <= 0) {
            return false;
        }

        // EXCEPTIONS TO FAST SHIPPING LABEL RULES.
        //  If there is stock only in JockeyPlaza, only the InStore label is shown
        if ($this->isOnlyStockInJockey($qty)) {
            return true;
        }

        //  If there is stock only in Caracol, only the fast label is shown
        if ($this->isOnlyStockInCaracol($qty)) {
            return false;
        }

        // Label available if at least one source has stock.
        if ($qty['sources'] >= self::GENERAL_MIN_STOCK) {
            $labelAvailable = true;
        }

        // GENERAL RULES FOR LURIN
        /*if ($this->generalRules($sku, $qty)) {
            $labelAvailable = true;
        }*/

        return $labelAvailable;
    }

    /**
     * Validate General Rules
     * @param $sku
     * @param $qty
     * @return bool
     * @throws NoSuchEntityException
     * @todo Set the configuration to get the GENERAL_MIN_STOCK value from the administrator.
     */
    public function generalRules($sku, $qty): bool
    {
        // GENERAL RULE: stock greater than or equal to 2, ignoring lurin.
        if ($qty['sources'] < self::GENERAL_MIN_STOCK) return false;

        // GENERAL RULE: stock in lurin.
        $product = $this->productRepository->get($sku);
        $manufacturer = $product->getAttributeText('manufacturer');
        $category = $product->getCategoria();

        // rules for category
        switch ($category) {
            case 'Fragancias':
            case 'Tratamiento':
            case 'Maquillaje':
                if ($qty['lurin'] < 3) return false;
                break;
            case 'Accesorios':
            case 'Capilar Profesional':
                if ($qty['lurin'] < 1) return false;
                break;
        }

        // rules for manufacturer
        switch ($manufacturer) {
            case 'Cela':
            case 'Creed':
            case 'Sisley':
            case 'Guerlain':
                if ($qty['lurin'] < 1) return false;
                break;
        }

        return true;
    }

    /**
     * Returns the max Stock Qty of sources and Lurin.
     *
     * @param $sku
     * @return array
     */
    public function getMaxQtyPerSource($sku): array
    {
        $max = 0;
        $qty = ['lurin' => 0, 'sources' => 0];

        $inventory = $this->sourceItemsBySku->execute($sku);

        foreach ($inventory as $source) {
            $sourceQuantity = $this->_inventoryBySourceHelper->getSalableQtyBySource($sku, $source);

            if ($source->getSourceCode() == AbstractBopisCollection::DEFAULT_BOPIS_SOURCE_CODE) {
                $qty['lurin'] = ($source->getStatus()) ? $sourceQuantity : 0;
                continue;
            }

            if (!$source->getStatus()) continue;

            if ($sourceQuantity > $max) {
                $max = $sourceQuantity;
                $qty['sources'] = $max;
            }

            $qty['per_source'][$source->getSourceCode()] = $sourceQuantity;
        }

        return $qty;
    }

    /**
     * Rule Exception.
     * Returns true if Jockey is the only source with stock.
     *
     * @param $qty
     * @return bool
     */
    public function isOnlyStockInJockey($qty): bool
    {
        return $this->thereIsOnlyStockIn(self::SOURCE_CODE_JOCKEY, $qty);
    }

    /**
     * Rule Exception.
     * Returns true if caracol is the only source with stock.
     *
     * @param $qty
     * @return bool
     */
    public function isOnlyStockInCaracol($qty): bool
    {
        return $this->thereIsOnlyStockIn(self::SOURCE_CODE_CARACOL, $qty);
    }

    /**
     * Returns true if the selected source is the only source with stock.
     *
     * @param $sourceCode
     * @param $qty
     * @return bool
     */
    public function thereIsOnlyStockIn($sourceCode, $qty): bool
    {
        if (!isset($qty['per_source']) || !isset($qty['per_source'][$sourceCode])) return false;

        //if ($qty['lurin'] > 0) return false;

        if ($qty['sources'] > $qty['per_source'][$sourceCode]) return false;

        $max = 0;
        $currentSourceStock = 0;
        foreach ($qty['per_source'] as $source_code => $stock) {
            if ($source_code == $sourceCode) {
                // If source_stock = 0, It can't be the only one with stock.
                if ($stock <= 0) return false;
                $currentSourceStock = $stock;
                continue;
            }

            if ($stock > $max) {
                $max = $stock;
            }
        }

        // If source_stock is more than the GENERAL_MIN_STOCK
        // AND the stock of others sources is 0.
        if ($currentSourceStock >= self::GENERAL_MIN_STOCK && $max <= 0) return true;

        return false;
    }

}

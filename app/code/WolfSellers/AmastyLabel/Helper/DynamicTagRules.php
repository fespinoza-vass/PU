<?php

namespace WolfSellers\AmastyLabel\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventoryApi\Api\GetSourceItemsBySkuInterface;
use WolfSellers\Bopis\Model\ResourceModel\AbstractBopisCollection;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\Product;

class DynamicTagRules extends AbstractHelper
{
    /** @var int */
    const FAST_METHOD_MIN_STOCK = 1;

    /** @var int */
    const INSTORE_MIN_STOCK = 1;

    /** @var int */
    const GENERAL_MIN_STOCK = 2;

    /**
     * @param Context $context
     * @param GetSourceItemsBySkuInterface $sourceItemsBySku
     * @param ProductRepository $productRepository
     */
    public function __construct(
        Context                                $context,
        protected GetSourceItemsBySkuInterface $sourceItemsBySku,
        protected ProductRepository            $productRepository
    )
    {
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
     * @throws NoSuchEntityException
     * @todo Set the configuration to get the FAST_METHOD_MIN_STOCK value from the administrator.
     */
    public function fastShippingLabel($qty, $sku): bool
    {
        $labelAvailable = false;

        // Label available if at least one source has stock.
        if ($qty['sources'] < self::FAST_METHOD_MIN_STOCK) return false;

        // GENERAL RULES FOR LURIN
        if ($this->generalRules($sku, $qty)) {
            $labelAvailable = true;
        }

        return $labelAvailable;
    }

    /**
     * Returns true if the product has the in-store label.
     *
     * @param $qty
     * @param $sku
     * @return bool
     * @throws NoSuchEntityException
     * @todo Set the configuration to get the INSTORE_MIN_STOCK value from the administrator.
     */
    public function InStoreLabel($qty, $sku): bool
    {
        $labelAvailable = false;

        // Label available if at least one source and lurin has stock.
        if ($qty['sources'] < self::INSTORE_MIN_STOCK || $qty['lurin'] < self::INSTORE_MIN_STOCK) {
            return false;
        }

        // GENERAL RULES FOR LURIN
        if ($this->generalRules($sku, $qty)) {
            $labelAvailable = true;
        }

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
            if ($source->getSourceCode() == AbstractBopisCollection::DEFAULT_BOPIS_SOURCE_CODE) {
                $qty['lurin'] = ($source->getStatus()) ? $source->getQuantity() : 0;
                continue;
            }

            if (!$source->getStatus()) continue;

            if ($source->getQuantity() > $max) {
                $max = $source->getQuantity();
                $qty['sources'] = $max;
            }
        }

        return $qty;
    }
}

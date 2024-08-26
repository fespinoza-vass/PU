<?php

namespace WolfSellers\Bopis\Block;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventoryApi\Api\GetSourceItemsBySkuInterface;
use WolfSellers\Bopis\Helper\Config;
use WolfSellers\Bopis\Helper\Data as Bopis;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Bundle\Model\Product\Type as ProductType;

class ShippingType extends AbstractProduct {


    private ProductType $productType;
    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepository;

    /**
     * @var GetSourceItemsBySkuInterface
     */
    private GetSourceItemsBySkuInterface $sourceItemsBySku;

    /**
     * @var Bopis
     */
    private Bopis $bopis;

    /**
     * @param Context $context
     * @param Config $config
     * @param ProductRepositoryInterface $productRepository
     * @param GetSourceItemsBySkuInterface $sourceItemsBySku
     * @param Bopis $bopis
     */
    public function __construct(
        Context $context,
        Config $config,
        ProductRepositoryInterface $productRepository,
        GetSourceItemsBySkuInterface $sourceItemsBySku,
        Bopis $bopis,
        ProductType $productType
    ) {
        parent::__construct($context);

        $this->config = $config;
        $this->productRepository = $productRepository;
        $this->sourceItemsBySku = $sourceItemsBySku;
        $this->bopis = $bopis;
        $this->productType = $productType;
    }

    /**
     * @return mixed
     */
    public function isActive() {
        return $this->config->isActive();
    }

    /**
     * @return Product|mixed|null
     * @throws NoSuchEntityException
     */
    public function getProduct() {
        if (!$this->_coreRegistry->registry('product') && $this->getProductId()) {
            $product = $this->productRepository->getById($this->getProductId());
            $this->_coreRegistry->register('product', $product);
        }

        return $this->_coreRegistry->registry('product');
    }

    /**
     * @return int|null
     */
    public function getProductId(): ?int {
        $product = $this->_coreRegistry->registry('product');

        return $product ? $product->getId() : null;
    }

    /**
     * @return bool
     * @throws NoSuchEntityException
     */
    public function applyStorePickup(): bool {
        $product = $this->getProduct();

        if($product->getTypeId() == 'bundle'){
            $sources =  $this->getAvailableSourcesBundleProduct($product);
            if(count($sources)>0){
                return true;
            }else{
                return false;
            }
        }

        return $this->getProductQuantity($product->getSku());
    }

    public function getProductQuantity($sku){
        $sources = $this->sourceItemsBySku->execute($sku);

        $currentCountry = $this->bopis->getCurrentCountry();

        $qty = 0;
        foreach ($sources as $source) {
            $_source = $this->bopis->getSource($source->getSourceCode());

            if (!$_source->isEnabled() || $_source->getCountryId() != $currentCountry) {
                continue;
            }

            $qty += $source->getQuantity();
        }

        return $qty > 0;
    }

    public function getAvailableSourcesBundleProduct($product){

        $selectionCollection = $product->getTypeInstance(true)->getSelectionsCollection(
            $product->getTypeInstance(true)->getOptionsIds($product),
            $product
        );

        $sourceAvailable = array();

        foreach ($selectionCollection as $child) {
            $sources = $this->sourceItemsBySku->execute($child->getSku());
            $currentCountry = $this->bopis->getCurrentCountry();

            foreach ($sources as $source) {
                $_source = $this->bopis->getSource($source->getSourceCode());

                if (!$_source->isEnabled() || $_source->getCountryId() != $currentCountry) {
                    continue;
                }

                if(intval($child->getSelectionQty()) <= $source->getQuantity()) {
                        $sourceAvailable[$child->getSku()][$source->getSourceCode()] = true;
                }
            }
        }

        return call_user_func_array('array_intersect_key', $sourceAvailable);
    }

    public function isVisible(){
        $product = $this->getProduct();
        $url = $this->getProduct()->getUrlKey();
        return strpos($this->getRequest()->getModuleName(), "checkout") !== false;
    }

    public function getUpdateUrl(){
        return $this->getProduct()->getProductUrl();
    }
}

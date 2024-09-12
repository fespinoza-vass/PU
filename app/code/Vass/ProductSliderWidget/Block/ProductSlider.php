<?php
/**
 * @copyright Copyright (c) 2024 VASS
 * @package Vass_ProductSliderWidget
 * @author VASS Team
 */

namespace Vass\ProductSliderWidget\Block;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Block\Product\Context as ParentContext;
use Magento\Catalog\Block\Product\Widget\Html\Pager;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\CatalogWidget\Model\Rule;
use Magento\Customer\Model\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Pricing\Render;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Rule\Model\Condition\Combine;
use Magento\Rule\Model\Condition\Sql\Builder;
use Magento\Widget\Block\BlockInterface;
use Magento\Widget\Helper\Conditions;

class ProductSlider extends AbstractProduct implements BlockInterface, IdentityInterface
{
    /**
     * Default value for products count that will be load
     */
    const DEFAULT_PRODUCTS_COUNT = 10;

    /**
     * Number of products to display in carrousel
     */
    const DEFAULT_PRODUCTS_SHOW_IN_DESKTOP = 5;
    const DEFAULT_PRODUCTS_SHOW_IN_TABLET = 3;
    const DEFAULT_PRODUCTS_SHOW_IN_MOBILE = 2;

    /**
     * Default value for products per page
     */
    const DEFAULT_PRODUCTS_PER_PAGE = 5;

    /**
     * Default value whether show pager or not
     */
    const DEFAULT_SHOW_PAGER = false;

    /**
     * Instance of pager block
     *
     * @var Pager
     */
    protected Pager $pager;

    /**
     * @var PriceCurrencyInterface
     */
    private PriceCurrencyInterface $priceCurrency;

    /**
     * Json Serializer Instance
     *
     * @var Json
     */
    private mixed $json;

    /**
     * @param ParentContext $context
     * @param CollectionFactory $productCollectionFactory
     * @param Visibility $catalogProductVisibility
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param Builder $sqlBuilder
     * @param Rule $rule
     * @param Conditions $conditionsHelper
     * @param array $data
     * @param Json|null $json
     */
    public function __construct(
        ParentContext $context,
        protected CollectionFactory $productCollectionFactory,
        protected Visibility $catalogProductVisibility,
        protected \Magento\Framework\App\Http\Context $httpContext,
        protected Builder $sqlBuilder,
        protected Rule $rule,
        protected Conditions $conditionsHelper,
        array $data = [],
        Json $json = null
    ) {
        $this->json = $json ?: ObjectManager::getInstance()->get(Json::class);
        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * @inheritdoc
     */
    protected function _construct(): void
    {
        parent::_construct();
        $this->addColumnCountLayoutDepend('empty', 6)
            ->addColumnCountLayoutDepend('1column', 5)
            ->addColumnCountLayoutDepend('2columns-left', 4)
            ->addColumnCountLayoutDepend('2columns-right', 4)
            ->addColumnCountLayoutDepend('3columns', 3);

        $this->addData([
            'cache_lifetime' => 86400,
            'cache_tags' => [Product::CACHE_TAG,
            ],]);
    }

    /**
     * Get key pieces for caching block content
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function getCacheKeyInfo(): array
    {
        $conditions = $this->getData('conditions')
            ? $this->getData('conditions')
            : $this->getData('conditions_encoded');

        return [
            'CATALOG_PRODUCTS_LIST_WIDGET',
            $this->getPriceCurrency()->getCurrency()->getCode(),
            $this->_storeManager->getStore()->getId(),
            $this->_design->getDesignTheme()->getId(),
            $this->httpContext->getValue(Context::CONTEXT_GROUP),
            (int)$this->getRequest()->getParam($this->getData('page_var_name'), 1),
            $this->getProductsPerPage(),
            $conditions,
            $this->json->serialize($this->getRequest()->getParams()),
            $this->getTemplate(),
            $this->getTitle()
        ];
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getProductPrice(Product $product): string {
        $priceRender =  $this->getLayout()->getBlock('product.price.render.default')
            ->setData('is_product_list', true);

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                FinalPrice::PRICE_CODE,
                $product,
                [
                    'include_container' => true,
                    'display_minimal_price' => true,
                    'zone' => Render::ZONE_ITEM_LIST,
                    'list_category_page' => true
                ]
            );
        }

        return $price;
    }

    /**
     * @inheritdoc
     */
    protected function _beforeToHtml(): ProductSlider|AbstractBlock
    {
        $this->setProductCollection($this->createCollection());
        return parent::_beforeToHtml();
    }

    /**
     * Prepare and return product collection
     *
     * @return Collection
     */
    public function createCollection(): Collection
    {
        /** @var $collection Collection */
        $collection = $this->productCollectionFactory->create();
        $collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());

        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addStoreFilter()
            ->setPageSize($this->getPageSize())
            ->setCurPage($this->getRequest()->getParam($this->getData('page_var_name'), 1));

        $conditions = $this->getConditions();
        $conditions->collectValidatedAttributes($collection);
        $this->sqlBuilder->attachConditionToCollection($collection, $conditions);

        /**
         * Prevent retrieval of duplicate records. This may occur when multiselect product attribute matches
         * several allowed values from condition simultaneously
         */
        $collection->distinct(true);

        return $collection;
    }

    /**
     * @return Combine
     */
    protected function getConditions(): Combine
    {
        $conditions = $this->getData('conditions_encoded')
            ? $this->getData('conditions_encoded')
            : $this->getData('conditions');

        if ($conditions) {
            $conditions = $this->conditionsHelper->decode($conditions);
        }

        foreach ($conditions as $key => $condition) {
            if (!empty($condition['attribute'])
                && in_array($condition['attribute'], ['special_from_date', 'special_to_date'])
            ) {
                $conditions[$key]['value'] = date('Y-m-d H:i:s', strtotime($condition['value']));
            }
        }

        $this->rule->loadPost(['conditions' => $conditions]);
        return $this->rule->getConditions();
    }

    /**
     * Retrieve how many products should be displayed
     *
     * @return int
     */
    public function getProductsCount(): int
    {
        if ($this->hasData('products_count')) {
            return $this->getData('products_count');
        }

        if (null === $this->getData('products_count')) {
            $this->setData('products_count', self::DEFAULT_PRODUCTS_COUNT);
        }

        return $this->getData('products_count');
    }

    /**
     * Retrieve how many products should be displayed
     *
     * @return int
     */
    public function getProductsPerPage(): int
    {
        if (!$this->hasData('products_per_page')) {
            $this->setData('products_per_page', self::DEFAULT_PRODUCTS_PER_PAGE);
        }
        return $this->getData('products_per_page');
    }

    /**
     * Return flag whether pager need to be shown or not
     *
     * @return bool
     */
    public function showPager(): bool
    {
        if (!$this->hasData('show_pager')) {
            $this->setData('show_pager', self::DEFAULT_SHOW_PAGER);
        }
        return (bool)$this->getData('show_pager');
    }

    /**
     * Retrieve how many products should be displayed on page
     *
     * @return int
     */
    protected function getPageSize(): int
    {
        return $this->showPager() ? $this->getProductsPerPage() : $this->getProductsCount();
    }

    /**
     * Render pagination HTML
     *
     * @return string
     */
    public function getPagerHtml(): string
    {
        if ($this->showPager() && $this->getProductCollection()->getSize() > $this->getProductsPerPage()) {
            if (!$this->pager) {
                $this->pager = $this->getLayout()->createBlock(
                    Pager::class,
                    'widget.products.list.pager'
                );

                $this->pager->setUseContainer(true)
                    ->setShowAmounts(true)
                    ->setShowPerPage(false)
                    ->setPageVarName($this->getData('page_var_name'))
                    ->setLimit($this->getProductsPerPage())
                    ->setTotalLimit($this->getProductsCount())
                    ->setCollection($this->getProductCollection());
            }
            if ($this->pager instanceof AbstractBlock) {
                return $this->pager->toHtml();
            }
        }
        return '';
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities(): array
    {
        $identities = [];
        if ($this->getProductCollection()) {
            foreach ($this->getProductCollection() as $product) {
                if ($product instanceof IdentityInterface) {
                    $identities = array_merge($identities, $product->getIdentities()); // phpcs:ignore
                }
            }
        }

        return $identities ?: [Product::CACHE_TAG];
    }

    /**
     * Get value of widgets' title parameter
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->getData('title');
    }

    /**
     * @return string
     */
    public function getHtmlId(): string
    {
        return $this->getData('html_id');
    }

    /**
     * @return PriceCurrencyInterface
     *
     * @deprecated 100.2.0
     */
    private function getPriceCurrency(): PriceCurrencyInterface
    {
        if ($this->priceCurrency === null) {
            $this->priceCurrency = ObjectManager::getInstance()
                ->get(PriceCurrencyInterface::class);
        }
        return $this->priceCurrency;
    }

    /**
     * @return int|string
     */
    public function getProductsToShowInDesktop(): int|string
    {
        $qty = $this->getData("desktop_products_qty");
        if (is_numeric($qty)) {
            return $this->formatedSlidesPerView((int)$qty);
        }
        return self::DEFAULT_PRODUCTS_SHOW_IN_DESKTOP;
    }

    /**
     * @return int|string
     */
    public function getProductsToShowInTablet(): int|string
    {
        $qty = $this->getData("tablet_products_qty");
        if (is_numeric($qty)) {
            return $this->formatedSlidesPerView((int)$qty);
        }
        return self::DEFAULT_PRODUCTS_SHOW_IN_TABLET;
    }

    /**
     * @return int|string
     */
    public function getProductsToShowInMobile(): int|string
    {
        $qty = $this->getData("mobile_products_qty");
        if (is_numeric($qty)) {
            return $this->formatedSlidesPerView((int)$qty);
        }
        return self::DEFAULT_PRODUCTS_SHOW_IN_MOBILE;
    }

    public function formatedSlidesPerView($value): string
    {
        if ($value == 0) {
            return "auto";
        }

        return $value;
    }
}

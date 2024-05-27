<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace WolfSellers\GoogleTagManager\Block;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Repository;
use Magento\CatalogRule\Api\CatalogRuleRepositoryInterface;
use Magento\CatalogRule\Model\ResourceModel\Rule;
use Magento\Cookie\Helper\Cookie;
use Magento\Customer\Model\Session\Proxy;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\GoogleTagManager\Helper\Data;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Google Analytics Block
 *
 * @api
 * @since 100.0.2
 */
class Ga extends \Magento\GoogleAnalytics\Block\Ga
{
    /**
     * @var \Magento\GoogleAnalytics\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Cookie\Helper\Cookie
     */
    protected $cookieHelper;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;
    private \Magento\Catalog\Helper\Data $_catalogHelper;
    private \Magento\Catalog\Helper\Image $imageHelper;
    private \Magento\CatalogRule\Api\CatalogRuleRepositoryInterface $catalogRuleRepository;
    private \Magento\Framework\Stdlib\DateTime\TimezoneInterface $_date;
    private \Magento\Customer\Model\Session\Proxy $sessionProxy;
    private \Magento\CatalogRule\Model\ResourceModel\Rule $ruleResource;
    private \Magento\Catalog\Api\CategoryRepositoryInterface $_categoryRepository;
    private \Magento\Catalog\Model\Product\Attribute\Repository $attributerepository;

    /**
     * @param Context $context
     * @param CollectionFactory $salesOrderCollection
     * @param Data $googleAnalyticsData
     * @param Cookie $cookieHelper
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param Repository $attributerepository
     * @param CategoryRepositoryInterface $categoryRepository
     * @param CatalogRuleRepositoryInterface $catalogRuleRepository
     * @param Rule $rule
     * @param StoreManagerInterface $storeManager
     * @param Proxy $sessionProxy
     * @param TimezoneInterface $date
     * @param Image $imageHelper
     * @param \Magento\Catalog\Helper\Data $catalogHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollection,
        \Magento\GoogleTagManager\Helper\Data $googleAnalyticsData,
        \Magento\Cookie\Helper\Cookie $cookieHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Catalog\Model\Product\Attribute\Repository $attributerepository,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\CatalogRule\Api\CatalogRuleRepositoryInterface $catalogRuleRepository,
        \Magento\CatalogRule\Model\ResourceModel\Rule $rule,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session\Proxy $sessionProxy,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Catalog\Helper\Data $catalogHelper,
        array $data = []
    ) {
        $this->cookieHelper = $cookieHelper;
        $this->jsonHelper = $jsonHelper;
        $this->attributerepository = $attributerepository;
        $this->_categoryRepository = $categoryRepository;
        $this->ruleResource = $rule;
        $this->_storeManager = $storeManager;
        $this->sessionProxy= $sessionProxy;
        $this->_date =  $date;
        $this->catalogRuleRepository = $catalogRuleRepository;
        $this->imageHelper = $imageHelper;
        parent::__construct($context, $salesOrderCollection, $googleAnalyticsData, $data, $cookieHelper);
        $this->_catalogHelper = $catalogHelper;
    }

    /**
     * Is gtm available
     *
     * @return bool
     */
    protected function _isAvailable()
    {
        return $this->_googleAnalyticsData->isGoogleAnalyticsAvailable();
    }

    /**
     * Render GA tracking scripts
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_isAvailable()) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * Get store currency code for page tracking javascript code
     *
     * @return string
     */
    public function getStoreCurrencyCode()
    {
        return $this->_storeManager->getStore()->getBaseCurrencyCode();
    }

    /**
     * Render information about specified orders and their items
     *
     * @return string
     * @deprecated 100.2.0 please use getOrdersDataArray method
     */
    public function getOrdersData()
    {
        $result = [];
        foreach ($this->getOrdersDataArray() as $orderDara) {
            $result[] = 'dataLayer.push(' . $this->jsonHelper->jsonEncode($orderDara) . ");\n";
        }
        return implode("\n", $result);
    }

    /**
     * Return information about order and items
     *
     * @return array
     * @since 100.2.0
     */
    public function getOrdersDataArray()
    {
        $result = [];
        $orderIds = $this->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return $result;
        }
        $collection = $this->_salesOrderCollection->create();
        $collection->addFieldToFilter('entity_id', ['in' => $orderIds]);

        /** @var \Magento\Sales\Model\Order $order */
        foreach ($collection as $order) {
            $orderData = [
                'id' => $order->getIncrementId(),
                'revenue' => $order->getBaseGrandTotal(),
                'tax' => $order->getBaseTaxAmount(),
                'shipping' => $order->getBaseShippingAmount(),
                'coupon' => (string)$order->getCouponCode()
            ];

            $products = [];
            /** @var \Magento\Sales\Model\Order\Item $item*/
            foreach ($order->getAllVisibleItems() as $item) {

                /** Get Name Categories of product */
                $categories = [];
                foreach($item->getProduct()->getCategoryIds() as $categoryId){
                    array_push($categories, $this->_categoryRepository->get($categoryId)->getName());
                }

                $category = isset($categories[0]) ? $categories[0] : '';
                $subcategory = isset($categories[1]) ? $categories[1] : '';
                $family = isset($categories[2]) ? $categories[2] : '';

                /** Get Rules of product */
                $rules = $this->getRules($item->getProduct()->getId());
                $dataRule = [];
                if($rules){
                    foreach ($rules as $rule){
                        $dataRule[] = $rule;
                    }
                }
                $dataRule = implode( ', ', $dataRule);

                $imageUrl = $this->imageHelper->init($item, 'product_base_image')->getUrl();

                //$category = !empty($item->getProduct()->getData('categoria')) ? $item->getProduct()->getData('categoria') : '';
                //$subcategory = !empty($item->getProduct()->getData('sub_categoria')) ? $item->getProduct()->getData('sub_categoria') : '';
                //$brand = !empty($item->getProduct()->getAttributeText('brand_ids')) ? $item->getProduct()->getAttributeText('brand_ids') : '';

                $options = $this->attributerepository->get('manufacturer')->getOptions();

                $brand = '';
                foreach($options as $options_value){
                    if($options_value->getValue() == $item->getProduct()->getData('manufacturer')){
                        $brand = $options_value->getLabel();
                    }
                }

                $gender = !empty($item->getProduct()->getAttributeText('genero')) ? $item->getProduct()->getAttributeText('genero') : '';
                $size = !empty($item->getProduct()->getAttributeText('tamano')) ? $item->getProduct()->getAttributeText('tamano') : '';

                $products[] = [
                    'id' => $item->getId(),
                    'name' => $item->getName(),
                    'sku' => $item->getSku(),
                    'price' => $item->getBasePrice(),
                    'category'  => $category,
                    'sub_categoria' => $subcategory,
                    'familia' => $family,
                    'genero'    => $gender,
                    'tamano'    => $size,
                    'quantity' => $item->getQtyOrdered(),
                    'promotion' => $dataRule,
                    'brand'     => $brand,
                    'productURL' => $item->getProduct()->getProductUrl(),
                    'imageURL' => $imageUrl

                ];
            }

            $result[] = [
                'ecommerce' => [
                    'purchase' => [
                        'actionField' => $orderData,
                        'products' => $products
                    ],
                    'currencyCode' => $this->getStoreCurrencyCode()
                ],
                'event' => 'purchase'
            ];
        }
        return $result;
    }

    /**
     * Check if user not allow to save cookie
     *
     * @return bool
     */
    public function isUserNotAllowSaveCookie()
    {
        return $this->cookieHelper->isUserNotAllowSaveCookie();
    }

    /*
     * Function to obtain product promotion
     */
    public function getRules($productId)
    {
        $date = $this->_date->date()->format('Y-m-d H:i:s');
        $websiteId = $this->_storeManager->getStore()->getWebsiteId();
        $customerGroupId = $this->sessionProxy->getCustomer()->getGroupId();

        $rules = $this->ruleResource->getRulesFromProduct($date, $websiteId, $customerGroupId, $productId);
        $promos = [];

        foreach ($rules as $rule){
            $promo = $this->catalogRuleRepository->get($rule['rule_id']);
            array_push($promos, $promo->getName());
        }

        return $promos;
    }

    /**
     * Retries current product to send via gtag
     * @return Product|null
     */
    public function getProductBlock(): ? Product
    {
        return $this->_catalogHelper->getProduct();
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCategoryName(): array
    {

        $product = $this->getProductBlock();
        $categories = [];

        if (empty($product)){

            return $categories;
        }

        foreach($product->getCategoryIds() as $categoryId){

            $categories[] = $this->_categoryRepository->get($categoryId)->getName();
        }
        return $categories;
    }

    /**
     * @return string|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCurrencyCode(): ?string
    {
        return $this->_storeManager->getStore()->getBaseCurrencyCode();
    }
}

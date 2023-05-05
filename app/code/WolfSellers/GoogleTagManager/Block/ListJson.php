<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace WolfSellers\GoogleTagManager\Block;

use Magento\Banner\Model\ResourceModel\Banner\CollectionFactory;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\Product\Attribute\Repository;
use Magento\Checkout\Helper\Cart;
use Magento\Customer\Model\Session;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Json\Helper\Data;
use Magento\Framework\Module\Manager;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Magento\GoogleTagManager\Model\Banner\Collector;
use Magento\Checkout\Model\Cart as CartModel;
use Magento\Catalog\Helper\Image;

class ListJson extends \Magento\GoogleTagManager\Block\ListJson
{
    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $_categoryRepository;
    protected $_cart;
    protected $imageHelper;

    public function __construct(
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\GoogleTagManager\Helper\Data $helper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Helper\Cart $checkoutCart,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Banner\Model\ResourceModel\Banner\CollectionFactory $bannerColFactory,
        \Magento\GoogleTagManager\Model\Banner\Collector $bannerCollector,
        \Magento\CatalogRule\Model\ResourceModel\Rule $rule,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session\Proxy $sessionProxy,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date,
        \Magento\CatalogRule\Api\CatalogRuleRepositoryInterface $catalogRuleRepository,
        Image $imageHelper,
        array $data = [],
        CartModel $cart )
    {
        $this->_categoryRepository = $categoryRepository;
        $this->_cart = $cart;
        $this->imageHelper = $imageHelper;
        $this->ruleResource = $rule;
        $this->_storeManager = $storeManager;
        $this->ruleResource = $rule;
        $this->_storeManager = $storeManager;
        $this->sessionProxy= $sessionProxy;
        $this->_date =  $date;
        $this->catalogRuleRepository = $catalogRuleRepository;
        parent::__construct(
            $context,
            $helper,
            $jsonHelper,
            $registry,
            $checkoutSession,
            $customerSession,
            $checkoutCart,
            $layerResolver,
            $moduleManager,
            $request,
            $bannerColFactory,
            $bannerCollector,
            $data
        );
    }

    public function getCurrencyCode() {
        return $this->_storeManager->getStore()->getBaseCurrencyCode();
    }

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\SessionException
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function getCartContentExtended()
    {
        $cart = [];
        $ids = [];
        $items = [];
        $skus = [];
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->getCheckoutSession()->getQuote();
        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach ($quote->getAllVisibleItems() as $item) {
            $item2 = $item->getProduct();
            $imageUrl = $this->imageHelper->init($item2, 'product_base_image')->getUrl();
            $attributes = $item2->getAttributes();
            $category = null;
            $subcategory = null;
            $brand = null;
            $gender = null;
            $size = null;
            foreach($attributes as $attribute){
                /*if($attribute->getName() === 'categoria') {
                    $category = $attribute->getFrontend()->getValue($item->getProduct());
                }
                if($attribute->getName() === 'sub_categoria') {
                    $subcategory = $attribute->getFrontend()->getValue($item->getProduct());
                }*/
                if($attribute->getName() === 'manufacturer') {
                    $brand = $attribute->getFrontend()->getValue($item->getProduct());
                }
                if($attribute->getName() === 'genero') {
                    $gender = $attribute->getFrontend()->getValue($item->getProduct());
                }
                if($attribute->getName() === 'tamano') {
                    $size = $attribute->getFrontend()->getValue($item->getProduct());
                    if( !$size ) $size = null;
                }
            }
            
            /** Get Rules of product */
            $rules = $this->getRules($item2->getId());
            $dataRule = [];
            if($rules){
                foreach ($rules as $rule){
                    $dataRule[] = $rule;
                }
            }
            $dataRule = implode( ', ', $dataRule);
            
            /** Get Name Categories of product */
            $categories = [];
            foreach($item2->getCategoryIds() as $categoryId){
                array_push($categories, $this->_categoryRepository->get($categoryId)->getName());
            }
            
            $category = isset($categories[0]) ? $categories[0] : '';
            $subcategory = isset($categories[1]) ? $categories[1] : '';
            
            $cartItem = [
                'id' => $item2->getId(),
                'name' => $item2->getName(),
                'sku' => $item2->getSku(),
                'price' => $item2->getFinalPrice(),
                'category' => $category,
                'sub_categoria' => $subcategory,
                'genero'    => $gender,
                'tamano'    => $size,
                'quantity' => $item->getQty(),
                'promotion' => $dataRule,
                'brand' => $brand,
                'productURL' => $item2->getProductUrl(),
                'imageURL' => $imageUrl,
            ];
            array_push($items, $cartItem);
            array_push($ids, $item2->getId());
            array_push($skus, $item2->getSku());
        }

        $cart = [
            'ids'   => $ids,
            'skus'  => $skus,
            'items' => $items,
            'totals' => [
                'subtotal' => $quote->getSubtotal(),
                'total' => $quote->getGrandTotal(),
                'discount' => $quote->getSubtotal() - $quote->getSubtotalWithDiscount()
            ],
            'applied_coupons' => [$quote->getCouponCode()]
        ];

        return $this->jsonHelper->jsonEncode($cart);
    }

    private function getCheckoutSession()
    {
        if (!$this->checkoutSession->isSessionExists()) {
            $this->checkoutSession->start();
        }
        return $this->checkoutSession;
    }

    public function getCategoryName(){
        $product = $this->getCurrentProduct();
        $categories = [];
        foreach($product->getCategoryIds() as $categoryId){
            array_push($categories, $this->_categoryRepository->get($categoryId)->getName());
        }
        return $categories;
    }

    public function getCustomerInfo()
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->getCheckoutSession()->getQuote();

        $customer = [
            'email'   => $quote->getCustomerEmail(),
            'first_name' => $quote->getCustomerFirstname(),
            'Last_name' => $quote->getCustomerLastname()
        ];

        return $this->jsonHelper->jsonEncode($customer);
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
}

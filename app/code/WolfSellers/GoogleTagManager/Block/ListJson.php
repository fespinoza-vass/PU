<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace WolfSellers\GoogleTagManager\Block;

use Magento\Catalog\Helper\Image;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\GoogleTagManager\Helper\Data AS GoogleHelper;
use Magento\Framework\Json\Helper\Data AS JsonHelperData;
use Magento\Framework\Registry;
use Magento\Checkout\Model\Session AS CheckoutSession;
use Magento\Customer\Model\Session;
use Magento\Checkout\Helper\Cart;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\Module\Manager;
use Magento\Framework\App\Request\Http;
use Magento\Banner\Model\ResourceModel\Banner\CollectionFactory;
use Magento\GoogleTagManager\Model\Banner\Collector;
use Magento\CatalogRule\Model\ResourceModel\Rule;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Session\Proxy;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\CatalogRule\Api\CatalogRuleRepositoryInterface;

class ListJson extends \Magento\GoogleTagManager\Block\ListJson
{

    /**
     * @var CategoryRepositoryInterface
     */
    protected CategoryRepositoryInterface $_categoryRepository;

    /**
     * @var Image
     */
    protected Image $imageHelper;

    /**
     * @var Rule
     */
    private Rule $ruleResource;

    /**
     * @var Proxy
     */
    private Proxy $sessionProxy;

    /**
     * @var TimezoneInterface
     */
    private TimezoneInterface $_date;

    /**
     * @var CatalogRuleRepositoryInterface
     */
    private CatalogRuleRepositoryInterface $catalogRuleRepository;

    /**
     * @param CategoryRepositoryInterface $categoryRepository
     * @param Context $context
     * @param GoogleHelper $helper
     * @param JsonHelperData $jsonHelper
     * @param Registry $registry
     * @param CheckoutSession $checkoutSession
     * @param Session $customerSession
     * @param Cart $checkoutCart
     * @param Resolver $layerResolver
     * @param Manager $moduleManager
     * @param Http $request
     * @param CollectionFactory $bannerColFactory
     * @param Collector $bannerCollector
     * @param Rule $rule
     * @param StoreManagerInterface $storeManager
     * @param Proxy $sessionProxy
     * @param TimezoneInterface $date
     * @param CatalogRuleRepositoryInterface $catalogRuleRepository
     * @param Image $imageHelper
     * @param array $data
     */
    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        Context $context,
        GoogleHelper $helper,
        JsonHelperData $jsonHelper,
        Registry $registry,
        CheckoutSession $checkoutSession,
        Session $customerSession,
        Cart $checkoutCart,
        Resolver $layerResolver,
        Manager $moduleManager,
        Http $request,
        CollectionFactory $bannerColFactory,
        Collector $bannerCollector,
        Rule $rule,
        StoreManagerInterface $storeManager,
        Proxy $sessionProxy,
        TimezoneInterface $date,
        CatalogRuleRepositoryInterface $catalogRuleRepository,
        Image $imageHelper,
        array $data = []
    )
    {
        $this->_categoryRepository = $categoryRepository;
        $this->imageHelper = $imageHelper;
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

    /**
     * @return string|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCurrencyCode() {
        return $this->_storeManager->getStore()->getBaseCurrencyCode();
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
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
            $family = null;
            $brand = null;
            $gender = null;
            $size = null;
            foreach($attributes as $attribute){
                if($attribute->getName() === 'categoria') {
                    $category = $attribute->getFrontend()->getValue($item->getProduct());
                }
                if($attribute->getName() === 'sub_categoria') {
                    $subcategory = $attribute->getFrontend()->getValue($item->getProduct());
                }
                if($attribute->getName() === 'familia') {
                    $family = $attribute->getFrontend()->getValue($item->getProduct());
                }
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
            /*$categories = [];
            foreach($item2->getCategoryIds() as $categoryId){
                array_push($categories, $this->_categoryRepository->get($categoryId)->getName());
            }

            $category = isset($categories[0]) ? $categories[0] : '';
            $subcategory = isset($categories[1]) ? $categories[1] : '';
            $family = isset($categories[2]) ? $categories[2] : '';*/

            $cartItem = [
                'id' => $item2->getId(),
                'name' => $item2->getName(),
                'sku' => $item2->getSku(),
                'price' => $item2->getFinalPrice(),
                'category' => $category,
                'sub_categoria' => $subcategory,
                'familia' => $family,
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

    /**
     * @return CheckoutSession
     * @throws \Magento\Framework\Exception\SessionException
     */
    private function getCheckoutSession()
    {
        if (!$this->checkoutSession->isSessionExists()) {
            $this->checkoutSession->start();
        }
        return $this->checkoutSession;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCategoryName(){
        $product = $this->getCurrentProduct();
        $categories = [];
        foreach($product->getCategoryIds() as $categoryId){
            array_push($categories, $this->_categoryRepository->get($categoryId)->getName());
        }
        return $categories;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\SessionException
     */
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

    /**
     * Function to obtain product promotion
     *
     * @param $productId
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
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

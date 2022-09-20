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
    protected CartModel $_cart;
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
        Image $imageHelper,
        array $data = [],
        CartModel $cart )
    {
        $this->_categoryRepository = $categoryRepository;
        $this->_cart = $cart;
        $this->imageHelper = $imageHelper;
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
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->getCheckoutSession()->getQuote();
        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach ($quote->getAllVisibleItems() as $item) {
            $item2 = $item->getProduct();
            $imageUrl = $this->imageHelper->init($item2, 'product_base_image')->getUrl();
            $attributes = $item2->getAttributes();
            $category1 = '';
            $category2 = '';
            $brand = '';
            foreach($attributes as $attribute){
                if($attribute->getName() === 'category_gear') {
                    $category1 = $attribute->getFrontend()->getValue($item2);
                }
                if($attribute->getName() === 'categoria') {
                    $category2 = $attribute->getFrontend()->getValue($item2);
                }
                if($attribute->getName() === 'brand_ids') {
                    $brand = $attribute->getFrontend()->getValue($item2);
                }
                $category = $category1 . ' / ' . $category2;
            }
            $cartItem = [
                'id' => $item2->getId(),
                'name' => $item2->getName(),
                'sku' => $item2->getSku(),
                'price' => $item2->getFinalPrice(),
                'category' => $category,
                'quantity' => $item->getQty(),
                'productURL' => $item2->getProductUrl(),
                'imageURL' => $imageUrl
            ];
            array_push($items, $cartItem);
            array_push($ids, $item2->getId());
        }

        $cart = [
            'ids'   => $ids,
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
        foreach($product->getCategoryIds() as $categoryId){
            return $this->_categoryRepository->get($categoryId)->getName();
        }
        return '';
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
}

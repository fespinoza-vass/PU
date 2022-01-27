<?php
/**
 * Webkul Software
 *
 * @category  Webkul
 * @package   Webkul_Mpsplitcart
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Mpsplitcart\Helper;

use Magento\Checkout\Model\Cart;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Mpsplitcart data helper.
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @var Magento\Checkout\Model\Cart
     */
    private $cart;

    /**
     * @var Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var Magento\Customer\Api\Data\CustomerInterfaceFactory
     */
    private $customerDataFactory;

    /**
     * @var \Magento\Customer\Model\Customer\Mapper
     */
    private $customerMapper;

    /**
     * @var Magento\Framework\Api\DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    private $productRepository;

    /**
     * @var \Webkul\Marketplace\Model\Product
     */
    private $mpModel;

    /**
     * @var \Webkul\Marketplace\Helper\Data
     */
    private $mpHelper;

    /**
     * @var \Webkul\Mpsplitcart\Cookie\Guestcart
     */
    private $guestCart;

    /**
     * @var \Webkul\Mpsplitcart\Logger\Logger
     */
    private $logger;

    /**
     * @var array
     */
    private $groupParams = [];

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param Cart $cart
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerInterfaceFactory $customerDataFactory
     * @param \Magento\Customer\Model\Customer\Mapper $customerMapper
     * @param DataObjectHelper $dataObjectHelper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Webkul\Marketplace\Model\Product $mpModel
     * @param \Webkul\Marketplace\Helper\Data $mpHelper
     * @param \Webkul\Mpsplitcart\Cookie\Guestcart $guestCart
     * @param \Webkul\Mpsplitcart\Logger\Logger $logger
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Customer\Model\Session $customerSession,
        Cart $cart,
        CustomerRepositoryInterface $customerRepository,
        CustomerInterfaceFactory $customerDataFactory,
        \Magento\Customer\Model\Customer\Mapper $customerMapper,
        DataObjectHelper $dataObjectHelper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Webkul\Marketplace\Model\Product $mpModel,
        \Webkul\Marketplace\Helper\Data $mpHelper,
        \Webkul\Mpsplitcart\Cookie\Guestcart $guestCart,
        \Webkul\Mpsplitcart\Logger\Logger $logger
    ) {
        $this->objectManager = $objectManager;
        $this->customerSession = $customerSession;
        parent::__construct($context);
        $this->cart = $cart;
        $this->customerRepository = $customerRepository;
        $this->customerDataFactory = $customerDataFactory;
        $this->customerMapper = $customerMapper;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->checkoutSession = $checkoutSession;
        $this->productRepository = $productRepository;
        $this->mpModel = $mpModel;
        $this->mpHelper = $mpHelper;
        $this->guestCart = $guestCart;
        $this->logger = $logger;
    }

    /**
     * getEnableSplitcartSettings used to get spitcart is enable or not
     *
     * @return int [returns 0 if disable else return 1]
     */
    public function getEnableSplitcartSettings()
    {
        try {
            return $this->scopeConfig->getValue(
                'marketplace/marketplacesplitcart_settings/mpsplitcart_enable',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_getEnableSplitcartSettings Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * getUpdatedQuote used to remove items of other sellers from the quote
     *
     * @param int $productSellerId [current seller checkout cart id]
     * @return void
     */
    public function getUpdatedQuote($productSellerId)
    {
        try {
            if ($productSellerId==0) {
                $productSellerId = 0;
            }
            $cart      = $this->cart->getQuote();
            $cartArray = [];
            $flag = false;

            foreach ($cart->getAllVisibleItems() as $item) {
                if (!$item->hasParentItemId()) {
                    $options = $item->getBuyRequest()->getData();
                    //checks for seller assign product
                    if (array_key_exists('mpassignproduct_id', $options)) {
                        $sellerId = $this->getSellerIdFromMpassign(
                            $options['mpassignproduct_id']
                        );
                    } else {
                        $sellerId = $this->getSellerId($item->getProductId());
                    }

                    if ($productSellerId !== $sellerId) {
                        $this->setCheckoutRemoveSession();
                        $this->cart->removeItem($item->getId());
                        $flag = true;
                    }
                }
            }
            if ($flag) {
                $this->saveCart();
                $this->updateCart();
            }
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_getUpdatedQuote Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * saveCart saves cart
     *
     * @return void
     */
    public function saveCart()
    {
        try {
            $this->cart->save();
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_saveCart Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * setCheckoutRemoveSession used to set a value in checkout session
     *
     * @return void
     */
    public function setCheckoutRemoveSession()
    {
        try {
            $this->checkoutSession->setWkRemoveItem(1);
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_setCheckoutRemoveSession Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * getCheckoutRemoveSession used to get a value from checkout session
     *
     * @return int
     */
    public function getCheckoutRemoveSession()
    {
        try {
            return $this->checkoutSession->getWkRemoveItem();
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_getCheckoutRemoveSession Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * unsetCheckoutRemoveSession used to unset value from checkout session
     *
     * @return void
     */
    public function unsetCheckoutRemoveSession()
    {
        try {
            $this->checkoutSession->unsWkRemoveItem();
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_unsetCheckoutRemoveSession Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * setWkCartWasUpdated used to set cart was updated true
     *
     * @return void
     */
    public function setWkCartWasUpdated()
    {
        try {
            $this->checkoutSession->setCartWasUpdated(true);
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_setWkCartWasUpdated Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * getVirtualCart used to get virtual cart of user
     *
     * @return array [returns virtual cart data]
     */
    public function getVirtualCart()
    {
        try {
            if ($this->customerSession->isLoggedIn()) {
                $customerId = $this->customerSession->getId();
                $customerData = [];
                $savedCustomerData = $this->customerRepository
                    ->getById($customerId);
                $customer = $this->customerDataFactory->create();
                //merge saved customer data with new values
                $customerData = array_merge(
                    $this->customerMapper->toFlatArray($savedCustomerData),
                    $customerData
                );
                if (array_key_exists('virtual_cart', $customerData)) {
                    $virtualCart = $customerData['virtual_cart'];
                    $virtualCart = json_decode($virtualCart, true);
                } else {
                    $virtualCart = "";
                }
            } else {
                $virtualCart = $this->guestCart->get();
                $virtualCart = json_decode($virtualCart, true);
            }
            return $virtualCart;
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_getVirtualCart Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * validateVirtualCart
     *
     * @param array $virtualCart
     * @return array
     */
    public function validateVirtualCart($virtualCart)
    {
        try {
            foreach ($virtualCart as $sellerId => $productArray) {
                foreach ($productArray as $productId => $itemInfo) {
                    if ($productId !== "grouped"
                        && array_key_exists('item_id', $itemInfo)
                        && $itemInfo['item_id'] == ""
                    ) {
                        unset($virtualCart[$sellerId][$productId]);
                    } elseif ($productId == "grouped") {
                        $virtualCart = $this->updateVirtualCartForGroupedProduct(
                            $itemInfo,
                            $virtualCart,
                            $sellerId
                        );
                    }
                    if (array_key_exists('grouped', $virtualCart[$sellerId])
                        && empty($virtualCart[$sellerId]['grouped'])
                    ) {
                        unset($virtualCart[$sellerId]['grouped']);
                    }
                    $check = $this->checkEmptyVirtualCart($virtualCart[$sellerId]);
                    if ($check) {
                        unset($virtualCart[$sellerId]);
                    }
                }
            }
            return $virtualCart;
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_validateVirtualCart Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * updateVirtualCartForGroupedProduct
     *
     * @param array $itemInfo
     * @param array $virtualCart
     * @param int $sellerId
     * @return array
     */
    private function updateVirtualCartForGroupedProduct($itemInfo, $virtualCart, $sellerId)
    {
        foreach ($itemInfo as $groupProId => $groupInner) {
            if (array_key_exists('item_id', $groupInner)
                && ($groupInner['item_id'] == "" || !array_key_exists('child', $groupInner))
            ) {
                unset($virtualCart[$sellerId]['grouped'][$groupProId]);
            }
        }

        return $virtualCart;
    }

    /**
     * setVirtualCart used to set virtual cart of user in customer session
     *
     * @param array $virtualCart [contains virtual cart data]
     * @return void
     */
    public function setVirtualCart($virtualCart)
    {
        try {
            if (!empty($virtualCart)) {
                $virtualCart = $this->validateVirtualCart($virtualCart);
            }
            $virtualCart = json_encode($virtualCart, true);

            if ($this->customerSession->isLoggedIn()) {
                $customerId  = $this->customerSession->getId();
                $customerData      = [];
                $savedCustomerData = $this->customerRepository
                    ->getById($customerId);

                $customer = $this->customerDataFactory->create();
                //merge saved customer data with new values
                $customerData = array_merge(
                    $this->customerMapper->toFlatArray($savedCustomerData),
                    $customerData
                );

                $customerData['virtual_cart'] = $virtualCart;
                $this->dataObjectHelper->populateWithArray(
                    $customer,
                    $customerData,
                    \Magento\Customer\Api\Data\CustomerInterface::class
                );
                //save customer
                $this->customerRepository->save($customer);
            } else {
                $this->guestCart->delete();
                $this->guestCart->set($virtualCart, 3600);
            }
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_setVirtualCart Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * updateVirtualCart used to update virtual cart data
     *
     * @param array $itemArray [item information]
     * @return void
     */
    public function updateVirtualCart($itemArray)
    {
        try {
            $virtualCart = $this->getVirtualCart();
            if ($virtualCart
                && is_array($virtualCart)
                && $itemArray !== null
                && $this->checkMpsplitcartStatus()
            ) {
                foreach ($virtualCart as $sellerId => $productArray) {
                    foreach ($productArray as $productId => $itemInfo) {
                        if ($productId !== "grouped"
                            && array_key_exists($productId, $itemArray)
                            && in_array($itemInfo['item_id'], $itemArray[$productId])
                        ) {
                            unset($virtualCart[$sellerId][$productId]);
                        } elseif ($productId == "grouped") {
                            foreach ($itemInfo as $groupProId => $groupInner) {
                                if (array_key_exists($groupProId, $itemArray)
                                    && in_array($groupInner['item_id'], $itemArray[$groupProId])
                                ) {
                                    unset($virtualCart[$sellerId]['grouped'][$groupProId]);
                                }
                            }
                        }
                    }
                    if (array_key_exists('grouped', $virtualCart[$sellerId])
                        && empty($virtualCart[$sellerId]['grouped'])
                    ) {
                        unset($virtualCart[$sellerId]['grouped']);
                    }
                    $check = $this->checkEmptyVirtualCart($virtualCart[$sellerId]);
                    if ($check) {
                        unset($virtualCart[$sellerId]);
                    }
                }
                $this->setVirtualCart($virtualCart);

                $quote   = $this->cart->getQuote();
                $itemIds = [];
                $proIds  = [];

                foreach ($quote->getAllVisibleItems() as $item) {
                    $itemIds[$item->getId()] = $item->getProductId();

                    $options = $item->getBuyRequest()->getData();
                    //checks for seller assign product
                    if (array_key_exists('mpassignproduct_id', $options)) {
                        $proIds[$item->getProductId()] = $options['mpassignproduct_id'];
                    }
                }

                if (!empty($virtualCart)
                    && is_array($virtualCart)
                    && $virtualCart !== ''
                    && $this->checkMpsplitcartStatus()
                ) {
                    $addCart = $this->prepareDataForCart($virtualCart, $itemIds, $proIds);
                    if ($addCart) {
                        $this->saveCart();
                        $quote->setTotalsCollectedFlag(false)->collectTotals();
                        $quote->save();
                    }
                }
                $this->unsetCheckoutRemoveSession();
                if (!$this->customerSession->isLoggedIn()) {
                    $this->addQuoteToVirtualCart();
                }
            }
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_updateVirtualCart Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * checkEmptyVirtualCart checks array empty or not
     *
     * @param array $data [virtual cart]
     * @return boolean
     */
    public function checkEmptyVirtualCart($data)
    {
        try {
            if (is_array($data) && count($data) <= 0) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_checkEmptyVirtualCart Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * addVirtualCartToQuote used to add products in cart from virtual cart
     *
     * @return void
     */
    public function addVirtualCartToQuote()
    {
        try {
            $quote       = $this->cart->getQuote();
            $virtualCart = $this->getVirtualCart();
            $oldVirtualCart = $virtualCart;

            $itemIds = [];
            $proIds  = [];
            foreach ($quote->getAllVisibleItems() as $item) {
                if (!$item->hasParentItemId()) {
                    $itemIds[$item->getId()] = $item->getProductId();

                    $options = $item->getBuyRequest()->getData();
                    //checks for seller assign product
                    if (array_key_exists('mpassignproduct_id', $options)) {
                        $proIds[$item->getProductId()] = $options['mpassignproduct_id'];
                    }
                }
            }

            if ($virtualCart
                && is_array($virtualCart)
                && $virtualCart !== ''
                && $this->checkMpsplitcartStatus()
            ) {
                $addCart = $this->prepareDataForCart($virtualCart, $itemIds, $proIds);
                if ($addCart) {
                    $this->saveCart();
                    $cartData = [];
                    foreach ($quote->getAllVisibleItems() as $item) {
                        $cartData[$item->getId()]['qty'] = $item->getQty();
                    }
                    if (!empty($cartData)) {
                        $cartData = $this->cart->suggestItemsQty($cartData);
                        try {
                            $this->cart->updateItems($cartData)->save();
                        } catch (\Exception $e) {
                            $this->logDataInLogger(
                                "Helper_Data_addVirtualCartToQuote_inner Exception : ".$e->getMessage()
                            );
                        }
                    }
                }
            }
            $this->checkMpQuoteSystem($oldVirtualCart);
            $this->unsetCheckoutRemoveSession();
            $this->updateCart();
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_addVirtualCartToQuote Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * checkQuoteExistsInVirtualCart
     *
     * @param object $item
     * @param int $quoteItemId
     * @param array $virtualCart
     * @return boolean
     */
    public function checkQuoteExistsInVirtualCart($item, $quoteItemId, $virtualCart)
    {
        try {
            if (!empty($virtualCart)) {
                foreach ($virtualCart as $sellerId => $data) {
                    if (array_key_exists('grouped', $data)
                        && array_key_exists($item->getProductId(), $data['grouped'])
                        && $data['grouped'][$item->getProductId()]['item_id'] == $quoteItemId
                    ) {
                        return true;
                    } elseif (!array_key_exists('grouped', $data)
                        && array_key_exists($item->getProductId(), $data)
                        && $data[$item->getProductId()]['item_id'] == $quoteItemId
                    ) {
                        return true;
                    }
                }
            }
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_checkQuoteExistsInVirtualCart Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * checkMpQuoteSystem used if any quote product was added into cart
     *
     * @param array $oldVirtualCart
     * @return void
     */
    public function checkMpQuoteSystem($oldVirtualCart)
    {
        try {
            $quote      = $this->cart->getQuote();
            $customerId = $this->customerSession->getId();
            $check      = $this->isModuleEnabled("Webkul_Mpquotesystem");

            if ($check && $customerId) {
                foreach ($quote->getAllVisibleItems() as $item) {
                    $options = $item->getBuyRequest()->getData();

                    if ($item->getParentItemId() === null
                        && $item->getItemId() > 0
                        && !array_key_exists('mpassignproduct_id', $options)
                    ) {
                        $model = $this->objectManager->get(
                            \Webkul\Mpquotesystem\Model\QuotesFactory::class
                        )->create();
                        $mpQuote = $model->getCollection()
                            ->addFieldToFilter(
                                'product_id',
                                $item->getProductId()
                            )->addFieldToFilter(
                                'item_id',
                                ['neq'=>0]
                            )->addFieldToFilter(
                                'status',
                                ['eq'=>2]
                            );

                        if ($mpQuote->getSize() > 0) {
                            $this->updateMpQuoteData($mpQuote, $item, $oldVirtualCart);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_checkMpQuoteSystem Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * updateMpQuoteData
     *
     * @param \Webkul\Mpquotesystem\Model\Quotes $mpQuote
     * @param object $item
     * @param array $oldVirtualCart
     * @return void
     */
    private function updateMpQuoteData($mpQuote, $item, $oldVirtualCart)
    {
        $res = false;
        $mpQuoteSystemHelper = $this->objectManager->get(
            \Webkul\Mpquotesystem\Helper\Data::class
        );
        $baseCurrencyCode    = $mpQuoteSystemHelper->getBaseCurrencyCode();
        $currentCurrencyCode = $mpQuoteSystemHelper->getCurrentCurrencyCode();
        $price = 0;
        $quoteId = 0;
        $quoteQty = 0;

        foreach ($mpQuote as $quote) {
            $res = $this->checkQuoteExistsInVirtualCart(
                $item,
                $quote->getItemId(),
                $oldVirtualCart
            );
            if ($res) {
                $price    = $quote->getQuotePrice();
                $quoteId  = $quote->getEntityId();
                $quoteQty = $quote->getQuoteQty();

                $quote->setItemId($item->getId());
                $quote->save();
            }
        }
        if ($res) {
            $priceOne = $mpQuoteSystemHelper->getwkconvertCurrency(
                $currentCurrencyCode,
                $baseCurrencyCode,
                $price
            );

            if ($quoteId != 0) {
                $item->setCustomPrice($priceOne);
                $item->setOriginalCustomPrice($priceOne);
                $item->setQty($quoteQty);
                $item->setRowTotal($priceOne * $quoteQty);
                $item->getProduct()->setIsSuperMode(true);
                $item->save();
            }
        }
    }

    /**
     * prepareDataForCart used to add product in cart
     *
     * @param array $virtualCart [contains virtual cart data of user]
     * @param array $itemIds     [contains item ids]
     * @param array $productIds  [contains product ids]
     * @return boolean
     */
    public function prepareDataForCart($virtualCart, $itemIds, $productIds)
    {
        try {
            $addCart = false;
            $this->groupParams = [];
            foreach ($virtualCart as $sellerId => $productArray) {
                foreach ($productArray as $productId => $itemData) {
                    if ($productId !== "grouped") {
                        $addCart = $this->addProductToCart($itemData, $itemIds, $productId, $productIds);
                    } elseif ($productId == "grouped") {
                        foreach ($itemData as $groupProId => $groupInner) {
                            $addCart = $this->addProductToCart($groupInner, $itemIds, $groupProId, $productIds);
                        }
                    }
                }
            }
            if (!empty($this->groupParams)) {
                foreach ($this->groupParams as $proId => $params) {
                    $_product = $this->productRepository
                        ->getById($proId);
                    if ($_product) {
                        $this->cart->addProduct($_product, $params);
                        $addCart = true;
                    }
                }
            }
            return $addCart;
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_prepareDataForCart Exception : ".$e->getMessage()
            );
            return false;
        }
    }

    /**
     * addProductToCart
     *
     * @param array $itemData
     * @param array $itemIds
     * @param int $productId
     * @param array $productIds
     * @return boolean
     */
    public function addProductToCart($itemData, $itemIds, $productId, $productIds)
    {
        try {
            $flag = false;
            if ($itemData['item_id'] && (!array_key_exists($itemData['item_id'], $itemIds))
                && ((!in_array($productId, $itemIds)
                    || (in_array($productId, $itemIds) && array_search($productId, $itemIds)!==$itemData['item_id']))
                || array_key_exists('mpassignproduct_id', $itemData)
                || array_key_exists($productId, $productIds))
            ) {
                $params = [];
                $params['qty'] = $itemData['qty'];
                $params['product'] = $productId;
                if (array_key_exists('mpassignproduct_id', $itemData)) {
                    $params['mpassignproduct_id'] = $itemData[
                        'mpassignproduct_id'
                    ];
                }

                if (array_key_exists('child', $itemData) && $itemData['child']!=='') {
                    $attributes = json_decode($itemData['child'], true);
                    $params = array_merge($params, $attributes);
                }
                if (array_key_exists('bundle_options', $itemData) && $itemData['bundle_options']!=='') {
                    $bundleItemData = json_decode($itemData['bundle_options'], true);
                    $params = array_merge($params, $bundleItemData);
                }

                try {
                    if (array_key_exists("super_product_config", $params)
                        && array_key_exists("product_type", $params["super_product_config"])
                        && $params["super_product_config"]["product_type"] == "grouped"
                        && array_key_exists("product_id", $params["super_product_config"])
                        && $params["super_product_config"]["product_id"] !== $productId
                    ) {
                        $tempProId = $productId;
                        $tempQty = $params['qty'];
                        $params['super_group'][$tempProId] = $tempQty;
                        $productId = $params["super_product_config"]["product_id"];
                        $params['product'] = $productId;
                        unset($params['qty']);
                        unset($params['super_product_config']);

                        if (array_key_exists($productId, $this->groupParams)) {
                            $this->groupParams[$productId]['super_group'][$tempProId] = $tempQty;
                        } else {
                            $this->groupParams[$productId] = $params;
                        }
                    } else {
                        $_product = $this->productRepository
                            ->getById($productId);
                        if ($_product) {
                            $this->cart->addProduct($_product, $params);
                            $flag = true;
                        }
                    }
                } catch (\Exception $e) {
                    $this->logDataInLogger(
                        "Helper_Data_addProductToCart_inner Exception : ".$e->getMessage()
                    );
                }
            }
            return $flag;
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_addProductToCart Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * checkSplitCart used to get all seller ids of products added in cart
     *
     * @return array [seller ids]
     */
    public function checkSplitCart()
    {
        try {
            $quote     = $this->cart->getQuote();
            $sellerIds = [];

            foreach ($quote->getAllVisibleItems() as $item) {
                if (!$item->hasParentItemId()) {
                    $options = $item->getBuyRequest()->getData();
                    //checks for seller assign product
                    if (array_key_exists('mpassignproduct_id', $options)) {
                        $sellerId = $this->getSellerIdFromMpassign(
                            $options['mpassignproduct_id']
                        );
                    } else {
                        $sellerId = $this->getSellerId($item->getProductId());
                    }
                    $sellerIds[] = $sellerId;
                }
            }
            $sellerIds = array_unique($sellerIds);

            return $sellerIds;
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_checkSplitCart Exception : ".$e->getMessage()
            );
        }
    }
    /**
     * getSellerId used to get seller id by giving a product id
     *
     * @param int $productid [contains product id]
     *
     * @return int [returns seller id]
     */
    public function getSellerId($productid)
    {
        try {
            $sellerId = 0;
            $model = $this->mpModel->getCollection()
                ->addFieldToFilter(
                    'mageproduct_id',
                    $productid
                );
            if ($model->getSize()) {
                foreach ($model as $value) {
                    $sellerId = $value->getSellerId();
                }
            }

            return $sellerId;
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_getSellerId Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * getMpCustomerId
     *
     * @return int
     */
    public function getMpCustomerId()
    {
        try {
            return $this->mpHelper->getCustomerId();
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_getMpCustomerId Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * getSellerIdFromMpassign used to get seller id
     * who has assigned the product of other seller.
     *
     * @param int $assignId [contains assign id]
     * @return int [returns seller id]
     */
    public function getSellerIdFromMpassign($assignId)
    {
        try {
            $sellerId = 0;
            $model = $this->objectManager->get(
                \Webkul\MpAssignProduct\Model\Items::class
            )->load($assignId);
            if ($model->getSellerId()) {
                $sellerId = $model->getSellerId();
            }

            return $sellerId;
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_getSellerIdFromMpassign Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * addQuoteToVirtualCart
     *
     * @return void
     */
    public function addQuoteToVirtualCart()
    {
        try {
            $quote = $this->cart->getQuote();
            $virtualCart = $this->getVirtualCart();

            if ($virtualCart == null
                || !is_array($virtualCart)
                || $virtualCart == ""
            ) {
                $virtualCart = [];
            }
            foreach ($quote->getAllVisibleItems() as $item) {
                $attributesData = [];
                $bundleOption = [];
                $productType = $item->getProductType();
                $options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
                $isGrouped = false;
                if ($productType=="grouped") {
                    $isGrouped = true;
                }
                if ($productType == "bundle" && $item->getHasChildren()) {
                    $bundleOption = $this->processBundleOption($options);
                } else {
                    $attributesData = $this->updateRequestData($options);
                }

                $productId = $item->getProductId();
                $options   = $item->getBuyRequest()->getData();

                //checks for seller assign product
                $result = $this->checkMpAssignProduct($options, $isGrouped, $virtualCart, $productId);
                $sellerId = $result['sellerId'];
                $virtualCart = $result['virtualCart'];

                $virtualCart = $this->updateVirtualCartItemData(
                    $isGrouped,
                    $virtualCart,
                    $sellerId,
                    $productId,
                    $item,
                    $attributesData
                );

                if (!empty($bundleOption)) {
                    $virtualCart[$sellerId][$productId]['bundle_options'] = json_encode($bundleOption, true);
                }
            }
            $this->setVirtualCart($virtualCart);
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_addQuoteToVirtualCart Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * updateVirtualCartItemData
     *
     * @param boolean $isGrouped
     * @param array $virtualCart
     * @param int $sellerId
     * @param int $productId
     * @param object $item
     * @param array $attributesData
     * @return array
     */
    private function updateVirtualCartItemData($isGrouped, $virtualCart, $sellerId, $productId, $item, $attributesData)
    {
        if ($isGrouped) {
            $virtualCart[$sellerId]['grouped'][$productId]['qty'] = $item->getQty();
            $virtualCart[$sellerId]['grouped'][$productId]['item_id'] = $item->getId();
            if (array_key_exists($productId, $virtualCart[$sellerId])
                && array_key_exists('item_id', $virtualCart[$sellerId][$productId])
                && $virtualCart[$sellerId][$productId]['item_id'] == $item->getId()
            ) {
                unset($virtualCart[$sellerId][$productId]);
            }
        } else {
            $virtualCart[$sellerId][$productId]['qty'] = $item->getQty();
            $virtualCart[$sellerId][$productId]['item_id'] = $item->getId();
        }

        if (!empty($attributesData)) {
            if ($isGrouped) {
                $virtualCart[$sellerId]['grouped'][$productId]['child'] = json_encode($attributesData, true);
            } else {
                $virtualCart[$sellerId][$productId]['child'] = json_encode($attributesData, true);
            }
        } else {
            if (array_key_exists('child', $virtualCart[$sellerId][$productId])) {
                unset($virtualCart[$sellerId][$productId]['child']);
            } elseif (array_key_exists('grouped', $virtualCart[$sellerId])
                && array_key_exists($productId, $virtualCart[$sellerId]['grouped'])
                && array_key_exists('child', $virtualCart[$sellerId]['grouped'][$productId])
            ) {
                unset($virtualCart[$sellerId]['grouped'][$productId]['child']);
            }
        }
        return $virtualCart;
    }

    /**
     * updateRequestData
     *
     * @param array $options
     * @return array
     */
    private function updateRequestData($options)
    {
        $attributesData = [];
        $attributesData = $options['info_buyRequest'];
        if (array_key_exists('qty', $attributesData)) {
            unset($attributesData['qty']);
        }
        if (array_key_exists('product', $attributesData)) {
            unset($attributesData['product']);
        }
        return $attributesData;
    }

    /**
     * checkMpAssignProduct
     *
     * @param array $options
     * @param boolean $isGrouped
     * @param array $virtualCart
     * @param int $productId
     * @return array
     */
    private function checkMpAssignProduct($options, $isGrouped, $virtualCart, $productId)
    {
        if (array_key_exists("mpassignproduct_id", $options)) {
            $mpAssignId = $options["mpassignproduct_id"];
            $sellerId = $this->getSellerIdFromMpassign(
                $mpAssignId
            );
            if ($isGrouped) {
                $virtualCart[$sellerId]['grouped'][$productId]['mpassignproduct_id'] = $mpAssignId;
            } else {
                $virtualCart[$sellerId][$productId]['mpassignproduct_id'] = $mpAssignId;
            }
        } else {
            $sellerId = $this->getSellerId($productId);
        }
        return [
            'sellerId' => $sellerId,
            'virtualCart' => $virtualCart
        ];
    }

    /**
     * processBundleOption
     *
     * @param array $options
     * @return array
     */
    private function processBundleOption($options)
    {
        $bundleOption = [];
        $bundleOption['selected_configurable_option'] = $options['info_buyRequest'][
            'selected_configurable_option'
        ];
        if (array_key_exists('bundle_option', $options['info_buyRequest'])) {
            $bundleOption['bundle_option'] = $options['info_buyRequest']['bundle_option'];
        }
        if (array_key_exists('bundle_option_qty', $options['info_buyRequest'])) {
            $bundleOption['bundle_option_qty'] = $options['info_buyRequest']['bundle_option_qty'];
        }
        return $bundleOption;
    }

    /**
     * updateCart
     *
     * @return void
     */
    public function updateCart()
    {
        try {
            $quote = $this->cart->getQuote();
            $quote->setTotalsCollectedFlag(false)->collectTotals();
            $quote->save();
            $this->setWkCartWasUpdated();
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_updateCart Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * isModuleEnabled checks a given module is enabled or not
     *
     * @param  string $moduleName
     * @return boolean
     */
    public function isModuleEnabled($moduleName)
    {
        try {
            return $this->_moduleManager->isEnabled($moduleName);
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_isModuleEnabled Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * isOutputEnabled checks a given module is enabled or not
     *
     * @param  string $moduleName
     * @return boolean
     */
    public function isOutputEnabled($moduleName)
    {
        try {
            return $this->_moduleManager->isOutputEnabled($moduleName);
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_isOutputEnabled Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * checkMpsplitcartStatus
     *
     * @return boolean
     */
    public function checkMpsplitcartStatus()
    {
        try {
            $moduleEnabled = $this->isModuleEnabled('Webkul_Mpsplitcart');
            $moduleOutputEnabled = $this->isOutputEnabled('Webkul_Mpsplitcart');
            if ($this->getEnableSplitcartSettings()
                && $moduleEnabled
                && $moduleOutputEnabled
            ) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_checkMpsplitcartStatus Exception : ".$e->getMessage()
            );
            return false;
        }
    }

    /**
     * getCatalogPriceIncludingTax
     *
     * @return boolean
     */
    public function getCatalogPriceIncludingTax()
    {
        try {
            $isShippingIncludingTax = $this->scopeConfig->getValue(
                'tax/calculation/shipping_includes_tax',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $displaySubTotal = $this->scopeConfig->getValue(
                'tax/cart_display/subtotal',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            if ($isShippingIncludingTax || in_array($displaySubTotal, [2,3])) {
                return true;
            }
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_getCatalogPriceIncludingTax Exception : ".$e->getMessage()
            );
            return false;
        }
    }

    /**
     * logDataInLogger
     *
     * @param string $data
     * @return void
     */
    public function logDataInLogger($data)
    {
        $this->logger->info($data);
    }

    /**
     * createCustomQuote
     *
     * @return void
     */
    public function createCustomQuote()
    {
        try {
            $checkoutCart = $this->objectManager->create(\Magento\Checkout\CustomerData\Cart::class);
            $this->checkoutSession->setWkCustomQuote(
                $checkoutCart->getSectionData()
            );
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_createCustomQuote Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * removeCustomQuote
     *
     * @return void
     */
    public function removeCustomQuote()
    {
        try {
            $this->checkoutSession->unsWkCustomQuote();
        } catch (\Exception $e) {
            $this->logDataInLogger(
                "Helper_Data_removeCustomQuote Exception : ".$e->getMessage()
            );
        }
    }
}

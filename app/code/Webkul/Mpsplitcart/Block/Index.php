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
namespace Webkul\Mpsplitcart\Block;

use Webkul\Marketplace\Helper\Data;
/**
 * Mpsplitcart Block
 */
class Index extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Webkul\Mpsplitcart\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    private $cartModel;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    private $priceHelper;
    /**
     * @var Data
     */
    private $_marketPlaceData;
    /**
     * @var \Webkul\MpFixedRateshipping\Helper\Data
     */
    private $_marketRatesShipping;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Webkul\Mpsplitcart\Helper\Data $helper
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param Data $marketPlaceData
     * @param \Webkul\MpFixedRateshipping\Helper\Data $marketRatesShipping
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Webkul\Mpsplitcart\Helper\Data $helper,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        Data $marketPlaceData,
        \Webkul\MpFixedRateshipping\Helper\Data $marketRatesShipping,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );
        $this->helper = $helper;
        $this->cartModel = $cart;
        $this->priceHelper = $priceHelper;
        $this->_marketPlaceData = $marketPlaceData;
        $this->_marketRatesShipping = $marketRatesShipping;
    }

    /**
     * getSellerData get seller array in order to
     * show items at shopping cart accr. to sellers
     *
     * @return array
     */
    public function getSellerData()
    {
        $cartArray = [];
        try {
            $cart = $this->cartModel->getQuote();
            foreach ($cart->getAllItems() as $item) {
                if (!$item->hasParentItemId()) {
                    $options = $item->getBuyRequest()->getData();

                    if (array_key_exists("mpassignproduct_id", $options)) {
                        $mpAssignId = $options["mpassignproduct_id"];
                        $sellerId = $this->helper->getSellerIdFromMpassign(
                            $mpAssignId
                        );
                        $sellerData = $this->_marketPlaceData->getSellerDataBySellerId($sellerId);
                        $sellerData = $sellerData->getData();
                        $shipping_amount = $this->_marketRatesShipping->getFreeShippingFrom($sellerId);
                        $shipping_charge = $this->_marketRatesShipping->getShippingCharges($sellerId);
                        
                    } else {
                        $sellerId = $this->helper->getSellerId($item->getProductId());
                        $sellerData = $this->_marketPlaceData->getSellerDataBySellerId($sellerId);
                        $sellerData = $sellerData->getData();
                        $shipping_amount = $this->_marketRatesShipping->getFreeShippingFrom($sellerId);
                        $shipping_charge = $this->_marketRatesShipping->getShippingCharges($sellerId);
                    }

                    $price =  $item->getRowTotal();

                    if ($this->helper->getCatalogPriceIncludingTax()) {
                        $price = $item->getRowTotalInclTax();
                    }

                    $formattedPrice = $this->priceHelper->currency(
                        $price,
                        true,
                        false
                    );
                    $cartArray[$sellerId][$item->getId()] = $formattedPrice;

                    if (!isset($cartArray[$sellerId]['total'])
                        || $cartArray[$sellerId]['total']==null
                    ) {
                        $cartArray[$sellerId]['total'] = $price;
                    } else {
                        $cartArray[$sellerId]['total'] += $price;
                    }

                    $formattedPrice = $this->priceHelper->currency(
                        $cartArray[$sellerId]['total'],
                        true,
                        false
                    );
                    $free_diff = $shipping_amount - $cartArray[$sellerId]['total'];
                    if($free_diff > 0)
                    { 
                        $free_diff =  $this->priceHelper->currency(
                            $free_diff,
                            true,
                            false
                        );
                    }else{
                        $free_diff = 0;
                        $shipping_charge = 0;
                    }

                    $shipping_charge = $this->priceHelper->currency(
                        $shipping_charge,
                        true,
                        false
                    );

                    
                    $cartArray[$sellerId]['formatted_total'] = $formattedPrice;
                    $cartArray[$sellerId]['seller_name'] = $sellerData[0]['shop_url'];
                    $cartArray[$sellerId]['min_amount'] = $shipping_amount;
                    $cartArray[$sellerId]['free_diff'] = $free_diff;
                    $cartArray[$sellerId]['charge_amount'] = $shipping_charge;
                    $cartArray[$sellerId]['accumulated percent']= 0;
                    $cartArray[$sellerId]['notification'] = 'Minimum Amount to get Free shipping';

                    if( empty($shipping_amount)){

                        $cartArray[$sellerId]['notification'] = 'The seller has no free-shipping amount';

                    }else{

                        if(floatval($cartArray[$sellerId]['total']) >= floatval($cartArray[$sellerId]['min_amount'])){

                            $cartArray[$sellerId]['accumulated percent'] = 100;

                        }elseif(floatval($cartArray[$sellerId]['min_amount']) > 0 ){

                            $min_amount = floatval($cartArray[$sellerId]['min_amount']);
                            $total = floatval($cartArray[$sellerId]['total']);

                            $cartArray[$sellerId]['accumulated percent'] = floor(  ($total * 100) / $min_amount );
                        }
                    }



                }
            }
        } catch (\Exception $e) {
            $this->helper->logDataInLogger(
                "Block_Index_getSellerData Exception : ".$e->getMessage()
            );
        }
        return $cartArray;
    }

    /**
     * getMpsplitcartEnable get splitcart is enable or not
     *
     * @return void
     */
    public function getMpsplitcartEnable()
    {
        try {
            return $this->helper->checkMpsplitcartStatus();
        } catch (\Exception $e) {
            $this->helper->logDataInLogger(
                "Block_Index_getMpsplitcartEnable Exception : ".$e->getMessage()
            );
        }
    }

    /**
     * getCartTotal used to get cart total
     *
     * @return string [returns formatted total price]
     */
    public function getCartTotal()
    {
        $cartTotal = 0;
        try {
            $cart = $this->cartModel->getQuote();
            foreach ($cart->getAllItems() as $item) {
                if (!$item->hasParentItemId()) {
                    $sellerId=$this->helper->getSellerId($item->getProductId());
                    $price =  $item->getProduct()->getQuoteItemRowTotal();

                    if (!$price) {
                        $price =  $item->getBaseRowTotal();
                    }
                    $cartTotal += $price;
                }
            }
            $formattedPrice = $this->priceHelper->currency(
                $cartTotal,
                true,
                false
            );
            $cartTotal = $formattedPrice;
        } catch (\Exception $e) {
            $this->helper->logDataInLogger(
                "Block_Index_getCartTotal Exception : ".$e->getMessage()
            );
        }
        return $cartTotal;
    }

}

<?php

namespace WolfSellers\Bopis\Plugin\Checkout\Controller;

use Magento\Checkout\Controller\Cart\Index;
use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Session;
use WolfSellers\Bopis\Helper\InventoryHelper;

class CartPlugin
{
    private Cart $cart;
    private InventoryHelper $inventoryHelper;
    private Session $checkoutSession;

    public function __construct(
        Cart $cart,
        InventoryHelper $inventoryHelper,
        Session $checkoutSession
    )
    {
        $this->cart = $cart;
        $this->inventoryHelper = $inventoryHelper;
        $this->checkoutSession = $checkoutSession;
    }

    public function beforeExecute(
        Index $subject
    ) {
        if ($this->checkoutSession->getWasValidated()){
            $skus = array_map(function ($item){return $item->getSku();},  $this->cart->getQuote()->getAllVisibleItems());
            if (sizeof($skus) <= 0) return $subject;
            if (sizeof(array_diff($this->checkoutSession->getValidatedItems(), $skus)) <= 0 && sizeof(array_diff($skus, $this->checkoutSession->getValidatedItems())) <= 0){
                return $subject;
            }
            $this->inventoryHelper->validate($this->cart->getQuote()->getId(), $this->cart->getQuote()->getAllVisibleItems());
            return $subject;
        }

        $this->inventoryHelper->validate($this->cart->getQuote()->getId(), $this->cart->getQuote()->getAllVisibleItems());
        return $subject;
    }

}

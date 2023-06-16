<?php

namespace WolfSellers\Bopis\Plugin\Checkout\Controller;

use Closure;
use Magento\Checkout\Controller\Index\Index;
use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Session;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use WolfSellers\Bopis\Helper\InventoryHelper;

class IndexPlugin
{
    private Session $checkoutSession;
    private Cart $cart;
    private ManagerInterface $messageManager;
    private RedirectFactory $resultRedirectFactory;
    private InventoryHelper $inventoryHelper;

    public function __construct(
        InventoryHelper $inventoryHelper,
        Session                  $checkoutSession,
        Cart                     $cart,
        ManagerInterface         $messageManager,
        RedirectFactory          $resultRedirectFactory
    )
    {
        $this->checkoutSession = $checkoutSession;
        $this->messageManager = $messageManager;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->cart = $cart;
        $this->inventoryHelper = $inventoryHelper;
    }

    public function aroundExecute(
        Index   $subject,
        Closure $proceed
    ) {
        if (!$this->inventoryHelper->isBopis($this->cart->getQuote()->getId())) return $proceed();

        if ($this->checkoutSession->getWasValidated()){
            $skus = array_map(function ($item){return $item->getSku();},  $this->cart->getQuote()->getAllVisibleItems());

            if ((sizeof(array_diff($this->checkoutSession->getValidatedItems(), $skus)) <= 0 && sizeof(array_diff($skus, $this->checkoutSession->getValidatedItems())) <= 0) AND $this->checkoutSession->getCanContinue()){
                return $proceed();
            }

            return $this->resultRedirectFactory->create()->setPath('checkout/cart/');
        }

        if(!$this->inventoryHelper->validate($this->cart->getQuote()->getId(), $this->cart->getQuote()->getAllVisibleItems())){
            return $this->resultRedirectFactory->create()->setPath('checkout/cart/');
        }

        return $proceed();
    }
}

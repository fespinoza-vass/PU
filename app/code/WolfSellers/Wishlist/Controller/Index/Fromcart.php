<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace WolfSellers\Wishlist\Controller\Index;

use Magento\Checkout\Helper\Cart as CartHelper;
use Magento\Checkout\Model\Cart as CheckoutCart;
use Magento\Framework\App\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultFactory;
use Magento\Wishlist\Controller\WishlistProviderInterface;
use Magento\Wishlist\Helper\Data as WishlistHelper;
use Magento\Wishlist\Model\ResourceModel\Wishlist\Collection;
use Magento\Wishlist\Model\ResourceModel\Wishlist\CollectionFactory;
use Magento\Wishlist\Model\Wishlist;
use Magento\Wishlist\Model\WishlistFactory;
use Zend\Log\Logger;

/**
 * Add cart item to wishlist and remove from cart controller.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Fromcart extends \Magento\Wishlist\Controller\AbstractIndex implements Action\HttpPostActionInterface
{
    /**
     * @var WishlistProviderInterface
     */
    protected $wishlistProvider;

    /**
     * @var WishlistHelper
     */
    protected $wishlistHelper;

    /**
     * @var CheckoutCart
     */
    protected $cart;

    /**
     * @var CartHelper
     */
    protected $cartHelper;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var Validator
     */
    protected $formKeyValidator;
    private WishlistFactory $wishlistFactory;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @param Context $context
     * @param WishlistProviderInterface $wishlistProvider
     * @param WishlistHelper $wishlistHelper
     * @param CheckoutCart $cart
     * @param CartHelper $cartHelper
     * @param Escaper $escaper
     * @param Validator $formKeyValidator
     * @param WishlistFactory $wishlistFactory
     */
    public function __construct(
        Action\Context $context,
        WishlistProviderInterface $wishlistProvider,
        WishlistHelper $wishlistHelper,
        CheckoutCart $cart,
        CartHelper $cartHelper,
        Escaper $escaper,
        Validator $formKeyValidator,
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory
    ) {
        parent::__construct($context);
        $this->wishlistProvider = $wishlistProvider;
        $this->wishlistHelper = $wishlistHelper;
        $this->cart = $cart;
        $this->cartHelper = $cartHelper;
        $this->escaper = $escaper;
        $this->formKeyValidator = $formKeyValidator;
        $this->wishlistFactory = $wishlistFactory;
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/wishlist.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $this->logger = $logger;
    }

    /**
     * Add cart item to wishlist and remove from cart
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @throws NotFoundException
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $resultRedirect->setPath('*/*/');
        }

        try {
            $itemId = (int)$this->getRequest()->getParam('item');
            $item = $this->cart->getQuote()->getItemById($itemId);
            if (!$item) {
                throw new LocalizedException(
                    __("The cart item doesn't exist.")
                );
            }
            /** @var Collection $wishlistSaveForLater */
            $wishlistSaveForLater = $this->wishlistFactory->create()->getCollection()
                ->addFieldToFilter('save_for_later', array('eq' => '1'));

            $productId = $item->getProductId();
            $buyRequest = $item->getBuyRequest();
            $this->logger->info("\WolfSellers\Wishlist\Controller\Index\Fromcart::execute ".$wishlistSaveForLater->count());
            if ($wishlistSaveForLater->count()){
                /**@var $wishlist Wishlist * */
                $wishlist = $wishlistSaveForLater->getFirstItem();
            }else{
                $this->logger->info("CUSTOMER ".$this->cart->getQuote()->getCustomerId());
                /**@var $wishlist Wishlist **/
                $wishlist = $this->wishlistProvider->getWishlist();
                $wishlist->setCustomerId($this->cart->getQuote()->getCustomerId());
                $wishlist->setName("Guardar para más tarde");
                $wishlist->setShared(1);
                $wishlist->setData("visibility", 0);
                $wishlist->setData("save_for_later", 1);
            }
            $wishlist->addNewItem($productId, $buyRequest);
            $wishlist->save();

            $this->cart->getQuote()->removeItem($itemId);
            $this->cart->save();

            $this->messageManager->addSuccessMessage(__(
                "Agregaste %1 a tu lista de guardar para después.",
                $this->escaper->escapeHtml($item->getProduct()->getName())
            ));
        } catch (LocalizedException $e) {
            $this->logger->info($e->getMessage());
            $this->logger->info($e->getTraceAsString());
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('We can\'t move the item to the wish list.'));
        }
        return $resultRedirect->setUrl($this->cartHelper->getCartUrl());
    }
}

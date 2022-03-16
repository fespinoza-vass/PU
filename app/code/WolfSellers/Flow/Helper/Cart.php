<?php

/**
 * Get Cart Information
 *
 */

namespace WolfSellers\Flow\Helper;

use Magento\Checkout\Model\Cart\RequestQuantityProcessor;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Helper\{AbstractHelper, Context};
use Magento\Checkout\Model\Cart as ModelCart;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Quote\Model\Quote\Item;
use Magento\Framework\Exception\{LocalizedException, NotFoundException};
use Magento\Framework\UrlInterface;

class Cart extends AbstractHelper
{
    /**
     * @var RequestQuantityProcessor
     */
    protected $quantityProcessor;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var Cart
     */
    protected $cart;

    /**
     * @var Cart
     */
    protected $priceHelper;

    /**
     * @var Cart
     */
    protected $imageHelper;

    /**
     * UrlInterface
     */
    protected $_url;

    /**
     * Cart constructor.
     * @param Context $context
     * @param CheckoutSession $checkoutSession
     * @param PriceHelper $priceHelper
     * @param ImageHelper $imageHelper
     * @param ModelCart $cart
     */
    public function __construct(
        Context $context,
        CheckoutSession $checkoutSession,
        PriceHelper $priceHelper,
        ImageHelper $imageHelper,
        ModelCart $cart,
        RequestQuantityProcessor $quantityProcessor,
        UrlInterface $url
    ){
        parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
        $this->cart = $cart;
        $this->priceHelper = $priceHelper;
        $this->imageHelper = $imageHelper;
        $this->quantityProcessor = $quantityProcessor;
        $this->_url = $url;
    }

    public function getItem($entityId, $type)
    {
        $item = null; $itemFound = false;
        foreach ( $this->checkoutSession->getQuote()->getAllItems() as $item) {
            if($entityId == $item->$type()){
                $itemFound = true;
                break;
            }
        }
        return [$itemFound, $item];
    }

    public function updateItemQty($item,$qty)
    {
        try{
            $this->updateItemQuantity($item, $qty);
            $cartData = null;
            $cartData[$item->getItemId()]['qty'] = $qty;
            $cartData = $this->quantityProcessor->process($cartData);
            $cartData = $this->cart->suggestItemsQty($cartData);
            $this->cart->updateItems($cartData)->save();
            return true;
        } catch (\Exception $e){
            return $e->getMessage();
        }
    }

    public function getMessageInformation($product,$item,$qty = 1)
    {
        try{
            if($item != null) {
                $qty = $item->getQty();
            }
            if($item != null) {
                $price = $item->getRowTotal();
            } else {
                $price = $product->getFinalPrice();
            }
            $imageUrl = $this->imageHelper->init($product, 'mini_cart_product_thumbnail')->getUrl();
            $a = [
                'product_name' => $product->getName(),
                'cart_url' => $this->getCartUrl(),
                'qty' => $qty,
                'total' => $this->priceHelper->currency($price),
                'image' => $imageUrl,
                'product_url' => $product->getProductUrl()
            ];
            return $a;
        } catch (\Exception $e){
            return [];
        }
    }

    /**
     * Updates quote item quantity.
     *
     * @param Item $item
     * @param float $qty
     * @return void
     * @throws LocalizedException
     */
    private function updateItemQuantity(Item $item, float $qty)
    {
        if ($qty > 0) {
            $item->clearMessage();
            $item->setQty($qty);

            if ($item->getHasError()) {
                throw new LocalizedException(__($item->getMessage()));
            }
        }
    }


    /**
     * Returns cart url
     *
     * @return string
     */
    private function getCartUrl()
    {
        return $this->_url->getUrl('checkout/cart', ['_secure' => true]);
    }
}

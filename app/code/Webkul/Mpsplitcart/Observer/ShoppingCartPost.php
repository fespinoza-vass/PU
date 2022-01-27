<?php
/**
 * Webkul Software
 *
 * @category  Webkul
 * @package   Webkul_Mpsplitcart
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Mpsplitcart\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Result\Page as ResultPage;

/**
 * Webkul Mpsplitcart ShoppingCartPost Observer
 */
class ShoppingCartPost implements ObserverInterface
{
    /**
     * @var \Webkul\Mpsplitcart\Helper\Data
     */
    private $helper;

    /**
     * @var ResultPage
     */
    protected $resultPage;

    /**
     * @param \Webkul\Mpsplitcart\Helper\Data $helper
     */
    public function __construct(
        \Webkul\Mpsplitcart\Helper\Data $helper,
        ResultPage $resultPage
    ) {
        $this->helper = $helper;
        $this->resultPage = $resultPage;
    }

    /**
     * [executes on controller_action_predispatch_checkout_cart_index event
     *  and used to add virtual cart items into quote]
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            if ($this->helper->checkMpsplitcartStatus()) {
                $this->resultPage->getLayout()->unsetElement('cart.summary');
                $this->resultPage->getLayout()->unsetElement('checkout.cart.coupon');
            }
        } catch (\Exception $e) {
            $this->helper->logDataInLogger("Observer_ShoppingCartPost execute : ".$e->getMessage());
        }
    }
}

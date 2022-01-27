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
use Magento\Framework\Exception\LocalizedException;

/**
 * Webkul Mpsplitcart CheckoutCartSaveAfterObserver Observer
 */
class CheckoutCartSaveAfterObserver implements ObserverInterface
{
    /**
     * @var \Webkul\Mpsplitcart\Helper\Data
     */
    private $helper;

    /**
     * @param \Webkul\Mpsplitcart\Helper\Data $helper
     */
    public function __construct(
        \Webkul\Mpsplitcart\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * [executes when checkout_cart_save_after event hit,
     * and used to update virtual cart]
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            if ($this->helper->checkMpsplitcartStatus()) {
                $this->helper->addQuoteToVirtualCart();
            }
        } catch (\Exception $e) {
            $this->helper->logDataInLogger("CheckoutCartSaveAfterObserver execute : ".$e->getMessage());
        }
    }
}

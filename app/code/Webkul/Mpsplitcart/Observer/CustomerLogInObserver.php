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

/**
 * Webkul Mpsplitcart CustomerLogInObserver Observer
 */
class CustomerLogInObserver implements ObserverInterface
{
    /**
     * @var Webkul\Mpsplitcart\Helper\Data
     */
    private $helper;

    /**
     * [__construct ]
     *
     * @param \Webkul\Mpsplitcart\Helper\Data $helper
     */
    public function __construct(
        \Webkul\Mpsplitcart\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * [executes on controller_action_predispatch_customer_account_logoutSuccess event]
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $session = $this->helper->unsetCheckoutRemoveSession();
        } catch (\Exception $e) {
            $this->helper->logDataInLogger("CustomerLogInObserver execute : ".$e->getMessage());
        }
    }
}

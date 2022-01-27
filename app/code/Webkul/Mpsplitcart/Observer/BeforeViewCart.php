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

namespace Webkul\Mpsplitcart\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;

/**
 * Webkul Mpsplitcart BeforeViewCart Observer
 */
class BeforeViewCart implements ObserverInterface
{
    /**
     * @var Webkul\Mpsplitcart\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $_urlInterface;

    /**
     * @param \Webkul\Mpsplitcart\Helper\Data $helper
     * @param ManagerInterface                $messageManager
     * @param \Magento\Framework\UrlInterface $urlInterface
     */
    public function __construct(
        \Webkul\Mpsplitcart\Helper\Data $helper,
        ManagerInterface $messageManager,
        \Magento\Framework\UrlInterface $urlInterface
    ) {
        $this->helper     = $helper;
        $this->messageManager = $messageManager;
        $this->_urlInterface = $urlInterface;
    }

    /**
     * [executes on controller_action_predispatch_checkout_index_index event]
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $result = $this->helper->checkSplitCart();
            $session = $this->helper->getCheckoutRemoveSession();

            if (count($result)>1
                && $this->helper->checkMpsplitcartStatus()
                && (!$session || $session!==1 || $session==null)
            ) {
                $this->messageManager->addError(
                    __(
                        'At a time you can checkout only one seller\'s products.
                        Remaining other products will be saved into your cart.'
                    )
                );

                $url = $this->_urlInterface->getUrl('checkout/cart');
                $observer->getControllerAction()
                    ->getResponse()
                    ->setRedirect($url);
            }
        } catch (\Exception $e) {
            $this->helper->logDataInLogger(
                "Observer_BeforeViewCart_execute Exception : ".$e->getMessage()
            );
        }
    }
}

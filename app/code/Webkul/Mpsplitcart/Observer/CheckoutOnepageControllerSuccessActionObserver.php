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

/**
 * Webkul Mpsplitcart CheckoutOnepageControllerSuccessActionObserver Observer
 */
class CheckoutOnepageControllerSuccessActionObserver implements ObserverInterface
{
    /**
     * @var \Webkul\Mpsplitcart\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Sales\Model\Order
     */
    private $order;

    /**
     * @param \Magento\Sales\Model\Order      $orderFactory
     * @param \Webkul\Mpsplitcart\Helper\Data $helper
     */
    public function __construct(
        \Magento\Sales\Model\Order $orderFactory,
        \Webkul\Mpsplitcart\Helper\Data $helper
    ) {
        $this->order = $orderFactory;
        $this->helper     = $helper;
    }

    /**
     * [executes when checkout_onepage_controller_success_action event hit,
     * and used to update virtual cart after successfully placed an order]
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            if ($this->helper->checkMpsplitcartStatus()) {
                $this->helper->removeCustomQuote();
                $orderIds = $observer->getOrderIds();
                $itemIds = [];
                foreach ($orderIds as $orderId) {
                    $orderInformation = $this->getOrderInfo($orderId);
                    foreach ($orderInformation->getAllVisibleItems() as $item) {
                        if (array_key_exists($item->getProductId(), $itemIds)) {
                            $itemIds[$item->getProductId()][] = $item->getQuoteItemId();
                        } else {
                            $itemIds[$item->getProductId()] = [$item->getQuoteItemId()];
                        }
                    }
                }
                $this->helper->updateVirtualCart($itemIds);
            }
        } catch (\Exception $e) {
            $this->helper->logDataInLogger(
                "CheckoutOnepageControllerSuccessActionObserver execute : ".$e->getMessage()
            );
        }
    }

    /**
     * getOrderInfo loads order
     *
     * @param integer $orderId [order id]
     * @return object
     */
    public function getOrderInfo($orderId)
    {
        try {
            $orderInformation = $this->order->load($orderId);
            return $orderInformation;
        } catch (\Exception $e) {
            $this->helper->logDataInLogger(
                "CheckoutOnepageControllerSuccessActionObserver getOrderInfo : ".$e->getMessage()
            );
        }
    }
}

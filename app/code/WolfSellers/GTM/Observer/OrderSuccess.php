<?php
namespace WolfSellers\GTM\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Registry;
use WolfSellers\GTM\Helper\Data as GtmHelper;
use WolfSellers\GTM\Block\Ga as GaBlock;

class OrderSuccess implements ObserverInterface
{
    protected $registry;
    protected $gtmHelper;

    public function __construct(
        Registry $registry,
        GtmHelper $gtmHelper
    ) {
        $this->registry = $registry;
        $this->gtmHelper = $gtmHelper;
    }

    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if ($order) {
            $orderData = $this->prepareOrderData($order);
            $this->registry->register('gtm_order_data', $orderData);
        }
    }

    private function prepareOrderData($order)
    {
        $items = [];
        foreach ($order->getAllVisibleItems() as $item) {
            $items[] = $this->gtmHelper->prepareProductData($item->getProduct());
        }

        return [
            'transactionId' => $order->getIncrementId(),
            'transactionAffiliation' => $order->getStoreName(),
            'transactionTotal' => $order->getGrandTotal(),
            'transactionTax' => $order->getTaxAmount(),
            'transactionShipping' => $order->getShippingAmount(),
            'transactionProducts' => $items
        ];
    }
}

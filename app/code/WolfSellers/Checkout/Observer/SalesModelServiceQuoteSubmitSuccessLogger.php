<?php

namespace WolfSellers\Checkout\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use WolfSellers\Checkout\Logger\Logger;

class SalesModelServiceQuoteSubmitSuccessLogger implements ObserverInterface
{
    /**
     * @var Logger
     */
    protected Logger $wolfLogger;

    /**
     * @param Logger $wolfLogger
     */
    public function __construct(
        Logger $wolfLogger
    )
    {
        $this->wolfLogger = $wolfLogger;
    }

    /**
     * This function writes essential data to track orders and potential issues.
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        try {
            /* @var $order \Magento\Sales\Model\Order */
            $order = $observer->getOrder();

            $orderData = [
                'incrementId' => $order->getIncrementId(),
                'customer_name' => $order->getCustomerName(),
                'customer_email' => $order->getCustomerEmail(),
                'delivery_method' => $order->getShippingMethod(),
                'delivery_postcode' => $order->getShippingAddress()->getPostcode(),
                'delivery_street' => $order->getShippingAddress()->getStreet(),
                'payment' => [
                    'method' => $order->getPayment()->getMethod(),
                    'amount' => $order->getPayment()->getAmountOrdered(),
                    'shipping_amount' => $order->getPayment()->getShippingAmount()
                ]
            ];

            $this->wolfLogger->info(print_r($orderData, true));
        } catch (\Exception $error) {
            $this->wolfLogger->error($error->getMessage());
        }
    }
}


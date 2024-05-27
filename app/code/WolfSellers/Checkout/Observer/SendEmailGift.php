<?php

namespace WolfSellers\Checkout\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use WolfSellers\Checkout\Helper\Email;
use Magento\Sales\Api\OrderRepositoryInterface;
class SendEmailGift implements ObserverInterface
{
    /**
     * @param Email $helperEmail
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        Email $helperEmail,
        OrderRepositoryInterface $orderRepository) {
        $this->helperEmail =$helperEmail;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param Observer $observer
     * @return void|null
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $orderId= $order->getId();
        $orderStatus=$order->getStatus();
        if ($orderStatus == 'processing') {
            return $this->helperEmail->sendEmail($orderId);
        }
    }
}

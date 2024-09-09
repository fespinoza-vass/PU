<?php

namespace Izipay\Core\Observer;

class SaveToOrder implements \Magento\Framework\Event\ObserverInterface
{   
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $event = $observer->getEvent();
        $quote = $event->getQuote();
    	$order = $event->getOrder();
        
        $order->setData('izipay_alternative_payment_method', $quote->getData('izipay_alternative_payment_method'));
        $order->setData('izipay_document_type', $quote->getData('izipay_document_type'));
        $order->setData('izipay_document_number', $quote->getData('izipay_document_number'));
        $order->setData('izipay_razon_social', $quote->getData('izipay_razon_social'));
        
        $order->setData('izipay_transaction_id', $quote->getData('izipay_transaction_id'));
        $order->setData('izipay_order_number', $quote->getData('izipay_order_number'));
        $order->setData('izipay_payment_code_response', $quote->getData('izipay_payment_code_response'));

    }
}
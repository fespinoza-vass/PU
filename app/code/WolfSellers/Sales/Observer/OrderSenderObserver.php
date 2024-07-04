<?php
namespace WolfSellers\Sales\Observer;

use Magento\Framework\Event\ObserverInterface;

class OrderSenderObserver implements ObserverInterface
{
    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Framework\App\Action\Action $controller */
        $transport = $observer->getTransport();
        $transport['ubigeo_estimated_delivery'] = $transport->getOrder()->getData('ubigeo_estimated_delivery');
    }
}

<?php


namespace WolfSellers\Checkout\Observer;


use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use WolfSellers\Urbano\Helper\Ubigeo;

class CheckoutSubmitAllAfterObserver implements ObserverInterface
{

    private Ubigeo $ubigeoHelper;


    public function __construct(
        Ubigeo $ubigeoHelper
    ) {
        $this->ubigeoHelper = $ubigeoHelper;
    }

    public function execute(Observer $observer)
    {
        $writer = new \Laminas\Log\Writer\Stream(BP . '/var/log/ubigeo.log');
        $logger = new \Laminas\Log\Logger();
        $logger->addWriter($writer);
        try {

            /* @var \Magento\Sales\Model\Order $order */
            $order = $observer->getOrder();
            $postCode = $order->getShippingAddress()->getPostcode();
            $ubigeoInfo = $this->ubigeoHelper->getDays($postCode);
            $_ubigeoInfo = $ubigeoInfo['data'];
            $logger->info($_ubigeoInfo);
            $order->setData("ubigeo_estimated_delivery", $_ubigeoInfo);
            $order->save();
        } catch (\Exception $e) {

            $logger->err($e->getMessage());
        }
    }
}

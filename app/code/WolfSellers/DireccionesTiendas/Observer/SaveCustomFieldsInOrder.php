<?php

namespace WolfSellers\DireccionesTiendas\Observer;

use Magento\Framework\Event\Observer;
use Psr\Log\LoggerInterface as Logger;

class SaveCustomFieldsInOrder implements \Magento\Framework\Event\ObserverInterface
{
    protected Logger $logger;

    public function __construct(
        Logger $logger
    )
    {
        $this->logger = $logger;
    }

    public function execute(Observer $observer)
    {
        //TODO Con el id del modelo, obtener la dirección según lo pide Savar
        //DEPARTAMENTO|PROVINCIA|DISTRITO
        try {
            $order = $observer->getEvent()->getOrder();
            $quote = $observer->getEvent()->getQuote();

            $value = "default";
            if (intval($quote->getDireccionestiendasId()) == 234) {
                $value = "It's the number 234";
            }
            $order->setData('direcciones_tiendas', $value);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return $this;
    }
}

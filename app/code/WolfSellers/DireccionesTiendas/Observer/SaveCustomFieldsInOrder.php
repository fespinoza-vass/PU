<?php

namespace WolfSellers\DireccionesTiendas\Observer;

use Magento\Framework\Event\Observer;
use Psr\Log\LoggerInterface as Logger;

use WolfSellers\DireccionesTiendas\Api\DireccionesTiendasRepositoryInterface as DireccionesTiendasRepository;

class SaveCustomFieldsInOrder implements \Magento\Framework\Event\ObserverInterface
{
    protected Logger $logger;
    protected DireccionesTiendasRepository $direccionesTiendasRepository;

    /**
     * @param Logger $logger
     * @param DireccionesTiendasRepository $direccionesTiendasRepository
     */
    public function __construct(
        Logger                       $logger,
        DireccionesTiendasRepository $direccionesTiendasRepository
    )
    {
        $this->logger = $logger;
        $this->direccionesTiendasRepository = $direccionesTiendasRepository;
    }

    /**
     * Save address data as Savar request: DEPARTAMENTO|PROVINCIA|DISTRITO
     * @param Observer $observer
     * @return $this|void
     */
    public function execute(Observer $observer)
    {
        //Dirección según lo pide Savar
        //DEPARTAMENTO|PROVINCIA|DISTRITO
        try {
            $order = $observer->getEvent()->getOrder();
            $quote = $observer->getEvent()->getQuote();

            $direccionEnviadaASavar = "NO uso Savar";
            if(!is_null($quote->getDireccionestiendasId())){
                $direccionTienda = $this->direccionesTiendasRepository->get(intval($quote->getDireccionestiendasId()));
                $depto = $direccionTienda->getDepartamento();
                $provi = $direccionTienda->getProvincia();
                $dist = $direccionTienda->getDistrito();
                $direccionEnviadaASavar = $depto . '|' . $provi . '|' . $dist;
                $direccionEnviadaASavar = strtoupper($direccionEnviadaASavar);
            }
            $order->setData('direcciones_tiendas', $direccionEnviadaASavar);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return $this;
    }
}

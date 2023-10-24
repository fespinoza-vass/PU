<?php

namespace WolfSellers\EnvioRapido\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\Data\OrderInterface;
use WolfSellers\EnvioRapido\Model\NotifyToSavar;
use WolfSellers\EnvioRapido\Model\SavarApi;




/**
 *
 */
class SavarHelper extends AbstractHelper
{

    /** @var OrderRepositoryInterface */
    protected $_orderRepository;
    /** @var NotifyToSavar */
    protected $_notifyToSavar;

    /**
     * @param Context $context
     * @param NotifyToSavar $notifyToSavar
     */
    public function __construct(
        Context $context,
        NotifyToSavar $notifyToSavar,
        OrderRepositoryInterface $orderRepository
    ){
        $this->_orderRepository = $orderRepository;
        $this->_notifyToSavar = $notifyToSavar;
        parent::__construct($context);
    }

    /**
     * @param OrderInterface $order
     * @return void
     */
    public function sendOrderToSavar(OrderInterface $order){

        $requestPayload = [
            "CodPaquete" => $order->getIncrementId(),
            "NomRemitente" => "Perfumerias Unidas",
            "DireccionRemitente" => "CALLE PRUEBA 123",
            "DistritoRemitente" => "LIMA|LIMA|CHORRILLOS",
            "TelefonoRemitente" => "959724456",
            "CodigoProducto" => $this->getSkuList($order),
            "MarcaProducto" => "",
            "ModeloProducto" => "",
            "ColorProducto" => "",
            "TipoProducto" => "fragil",
            "DescProducto" => "fragil",
            "cantidad" => count($order->getItems()),
            "NomConsignado" => $order->getCustomerFirstname() . " ". $order->getCustomerLastname(),
            "NumDocConsignado" => "Max20_4156565",
            "DireccionConsignado" => "Avenida Rio tiber",
            "DistritoConsignado" => "LIMA|LIMA|CHORRILLOS",
            "Referencia" => "OPCIONAL_Cerca hay un parque",
            "TelefonoConsignado" => "959878007",
            "CorreoConsignado" => $order->getCustomerEmail(),
            "Subservicio" => "same day",
            "TipoPago" => "PREPAGADO",
            "MetodoPago" => "", // no se utiliza al ser PREPAGADO
            "Monto" => $order->getGrandTotal(),
            "Largo" => "",
            "Ancho" => "",
            "Alto" => "",
            "Peso" => "",
            "ValorComercial" => $order->getGrandTotal(),
            "HoraIni1" => "09:00",
            "HoraFin1" => "12:00",
            "HoraIni2" => "14:00",
            "HoraFin2" => "18:00",
            "Comentario" => "Entregar a la puerta principal",
            "Comentario2" => "prueba comentario dos",
            "Latitud" => "",
            "Longitud" => ""
        ];

        $result = $this->_notifyToSavar->execute($requestPayload);


    }

    /**
     * @param OrderInterface $order
     * @return string
     */
    protected function getSkuList(OrderInterface $order){
        $skuList = [];
        foreach($order->getItems() as $item){
            $skuList[] = $item->getSku();
        }
        return implode(",",$skuList);
    }
}

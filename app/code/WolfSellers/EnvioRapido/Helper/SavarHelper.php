<?php

namespace WolfSellers\EnvioRapido\Helper;

use Magento\Eav\Model\Config;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\Data\OrderInterface;
use WolfSellers\DireccionesTiendas\Api\DireccionesTiendasRepositoryInterface;
use WolfSellers\EnvioRapido\Model\NotifyToSavar;
use WolfSellers\EnvioRapido\Model\SavarApi;
use Magento\InventoryApi\Api\SourceRepositoryInterface;



/**
 *
 */
class SavarHelper extends AbstractHelper
{
    /**
     * @var Json
     */
    private Json $json;

    /** @var Config */
    protected $_eavConfig;
    /** @var SourceRepositoryInterface */
    protected $_sourceRepository;

    /** @var DireccionesTiendasRepositoryInterface */
    protected $_direccionesTiendasRepository;

    /** @var OrderRepositoryInterface */
    protected $_orderRepository;
    /** @var NotifyToSavar */
    protected $_notifyToSavar;

    /**
     * @param Context $context
     * @param NotifyToSavar $notifyToSavar
     */
    public function __construct(
        Context                               $context,
        NotifyToSavar                         $notifyToSavar,
        OrderRepositoryInterface              $orderRepository,
        DireccionesTiendasRepositoryInterface $direccionesTiendasRepository,
        SourceRepositoryInterface             $sourceRepository,
        Config                                $eavConfig,
        Json                                  $json
    )
    {
        $this->json = $json;
        $this->_sourceRepository = $sourceRepository;
        $this->_orderRepository = $orderRepository;
        $this->_notifyToSavar = $notifyToSavar;
        $this->_direccionesTiendasRepository = $direccionesTiendasRepository;
        $this->_eavConfig = $eavConfig;
        parent::__construct($context);
    }

    /**
     * @param OrderInterface $order
     * @return void
     */
    public function sendOrderToSavar(OrderInterface $order)
    {
        $distrito = $order->getShippingAddress()->getDistritoEnvioRapido();

        $distritoTienda = $this->_direccionesTiendasRepository->get($distrito);
        $sourceCode = $distritoTienda->getTienda();

        $source = $this->_sourceRepository->get($sourceCode);

        $requestPayload = [
            "CodPaquete" => $order->getIncrementId(),
            "NomRemitente" => "Perfumerias Unidas",
            "DireccionRemitente" => $source->getStreet(),
            "DistritoRemitente" => $source->getRegion() . "|" . $source->getCity() . "|" . $source->getDistrict(),
            "TelefonoRemitente" => $source->getPhone(),
            "CodigoProducto" => $this->getSkuList($order),
            "MarcaProducto" => "",
            "ModeloProducto" => "",
            "ColorProducto" => "",
            "TipoProducto" => "fragil",
            "DescProducto" => "fragil",
            "cantidad" => count($order->getItems()),
            "NomConsignado" => $order->getCustomerFirstname() . " " . $order->getCustomerLastname(),
            "NumDocConsignado" => $order->getShippingAddress()->getVatId(),
            "DireccionConsignado" => implode(",",$order->getShippingAddress()->getStreet()),
            "DistritoConsignado" => $source->getRegion() . "|" . $source->getCity() . "|" . $source->getDistrict(),
            "Referencia" => $order->getShippingAddress()->getReferenciaEnvio(),
            "TelefonoConsignado" => $order->getShippingAddress()->getTelephone(),
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
            "HoraIni1" => "",
            "HoraFin1" => "",
            "HoraIni2" => "",
            "HoraFin2" => "",
            "Comentario" => "",
            "Comentario2" => "",
            "Latitud" => "",
            "Longitud" => ""
        ];

        if ($order->getShippingAddress()->getHorariosDisponibles()) {
            $label = $this->getValueByOptionId('horarios_disponibles', $order->getShippingAddress()->getHorariosDisponibles());

            $horaInici1 = "";
            $horaFin1 = "";
            $subServicio = "";

            switch($label){
                case '12_4_hoy':
                    $horaInici1 = "12:00";
                    $horaFin1   = "16:00";
                    $subServicio = "Same Day";
                case '4_8_hoy':
                    $horaInici1 = "16:00";
                    $horaFin1   = "20:00";
                    $subServicio = "Same Day";
                case '12_4_manana':
                    $horaInici1 = "12:00";
                    $horaFin1   = "16:00";
                    $subServicio = "Next Day";
                case '4_8_manana':
                    $horaInici1 = "16:00";
                    $horaFin1   = "20:00";
                    $subServicio = "Next Day";
            }

            $requestPayload['HoraIni1'] = $horaInici1;
            $requestPayload['HoraFin1'] = $horaFin1;
            $requestPayload['Subservicio'] = $subServicio;
        }

        $result = $this->_notifyToSavar->execute($requestPayload);

        $order->setSavarStatus($this->json->serialize($result));

        $this->_orderRepository->save($order);

        return $result;
    }

    /**
     * @param OrderInterface $order
     * @return string
     */
    protected function getSkuList(OrderInterface $order)
    {
        $skuList = [];
        foreach ($order->getItems() as $item) {
            $skuList[] = $item->getSku();
        }
        return implode(",", $skuList);
    }

    public function getValueByOptionId($attributeCode, $optionId)
    {
        $label = null;
        $attribute = $this->_eavConfig->getAttribute('customer_address', $attributeCode);
        $options = $attribute->getSource()->getAllOptions();
        foreach ($options as $option) {
            if ($option['value'] == $optionId) {
                $label = $option['label'];
            }
        }
        return $label;
    }
}

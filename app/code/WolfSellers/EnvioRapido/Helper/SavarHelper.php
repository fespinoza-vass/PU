<?php

namespace WolfSellers\EnvioRapido\Helper;

use Magento\Eav\Model\Config;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\Data\OrderInterface;
use WolfSellers\DireccionesTiendas\Api\DireccionesTiendasRepositoryInterface;
use WolfSellers\EnvioRapido\Model\NotifyToSavarCreateOrder;
use WolfSellers\EnvioRapido\Model\GetSavarOrder;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\Convert\Order as OrderConverter;
use Magento\Shipping\Model\ShipmentNotifier as OrderShipmentNotifier;
use WolfSellers\EnvioRapido\Logger\Logger as SavarLogger;
use Magento\InventoryApi\Api\SourceRepositoryInterface;




/**
 *
 */
class SavarHelper extends AbstractHelper
{
    /**
     *
     */
    CONST SHIPPING_METHOD_ENVIO_RAPIDO = "envio_rapido_envio_rapido";


    /** @var SavarLogger */
    protected $_savarLogger;
    /** @var SearchCriteriaBuilder */
    protected $_searchCriteriaBuilder;

    /** @var OrderShipmentNotifier */
    protected $_orderShipmentNotifier;

    /** @var OrderConverter */
    protected $_orderConverter;

    /** @var OrderFactory */
    protected $_orderFactory;

    /** @var GetSavarOrder */
    protected $_getSavarOrder;
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
    /** @var NotifyToSavarCreateOrder */
    protected $_notifyToSavar;

    /**
     * @param Context $context
     * @param NotifyToSavarCreateOrder $notifyToSavar
     */
    public function __construct(
        Context                               $context,
        NotifyToSavarCreateOrder              $notifyToSavar,
        OrderRepositoryInterface              $orderRepository,
        DireccionesTiendasRepositoryInterface $direccionesTiendasRepository,
        SourceRepositoryInterface             $sourceRepository,
        Config                                $eavConfig,
        Json                                  $json,
        GetSavarOrder                         $getSavarOrder,
        OrderFactory                          $orderFactory,
        OrderConverter                        $orderConverter,
        OrderShipmentNotifier                 $orderShipmentNotifier,
        SearchCriteriaBuilder                 $searchCriteriaBuilder,
        SavarLogger                           $savarLogger

    )
    {
        $this->_savarLogger = $savarLogger;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_orderShipmentNotifier = $orderShipmentNotifier;
        $this->_orderConverter = $orderConverter;
        $this->_orderFactory = $orderFactory;
        $this->_getSavarOrder = $getSavarOrder;
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
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
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

        $response = $this->json->serialize($result['response']);
        if($result['state_code'] == 200){
            $order->addStatusHistoryComment('Se genero correctamente la orden en SAVAR con registro: '.$response);
            $this->_savarLogger->info("Se creo en savar la orden:".$order->getIncrementId());
        }else{
            $order->addStatusHistoryComment("No fue posible generar la orden en savar, estatus: ".$result['state_code']. " ". $response);
            $this->_savarLogger->error("No fue posible generar la orden en savar, estatus: ".$result['state_code']. " ". $response);
        }

        $this->_orderRepository->save($order);

        return $result;
    }

    /**
     * @param $orderIncremental
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSavarOrder($orderIncremental)
    {
        $result = $this->_getSavarOrder->execute($orderIncremental);

        $order = $this->_orderFactory->create()->loadByIncrementId($orderIncremental);

        $response = $this->json->serialize($result['response']);
        if($result['state_code'] != 200){
            $this->_savarLogger->error( __("No fue posible consultar la orden $orderIncremental: ".$result['state_code']. " ". $response));
            return false;
        }

        if(intval($result['response']['vcodestado']) == 9){
            if (!$order->canShip() || !$order->hasShipments()) {
                $this->_savarLogger->error( __('You can\'t create an shipment.'));
            }
        }
    }

    /**
     * @param $order
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function generateShipment($order){
        $shipment = $this->_orderConverter->toShipment($order);

        foreach ($order->getAllItems() AS $orderItem) {
            if (! $orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                continue;
            }
            $qtyShipped = $orderItem->getQtyToShip();
            $shipmentItem = $this->_orderConverter->itemToShipmentItem($orderItem)->setQty($qtyShipped);
            $shipment->addItem($shipmentItem);
        }

        $shipment->register();
        $shipment->getOrder()->setIsInProcess(true);
        $shipment->getExtensionAttributes()->setSourceCode('1');

        try {

            $shipment->save();
            $shipment->getOrder()->save();

            $this->_orderShipmentNotifier
                ->notify($shipment);

            $shipment->save();
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __($e->getMessage())
            );
        }
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

    /**
     * @param $attributeCode
     * @param $optionId
     * @return mixed|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
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

    /**
     * @return void
     */
    public function updateSavarOrders(){
        $this->_savarLogger->error("consulta de ordenes savar");
        $searchCriteria = $this->_searchCriteriaBuilder
            ->addFilter('shipping_method',self::SHIPPING_METHOD_ENVIO_RAPIDO,"eq")
            ->addFilter('status',"order_on_the_way","eq")
            ->create();

        $orders = $this->_orderRepository->getList($searchCriteria);

        if($orders->getTotalCount() > 0){
            /** @var OrderInterface $order */
            foreach($orders as $order){
                try {
                    $this->getSavarOrder($order->getIncrementId());
                }catch (\Throwable $error){
                    $this->_savarLogger->error("error al actualizar estatus de orden savar: ".$order->getIncrementId());
                    continue;
                }
            }
        }
    }
}

<?php

namespace WolfSellers\EnvioRapido\Model\Carrier;

use Psr\Log\LoggerInterface;

use Magento\Framework\App\Config\ScopeConfigInterface;

use Magento\Catalog\Api\ProductRepositoryInterface as ProductRepository;
use Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku as SalableQtyBySku;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Quote\Model\Quote\Address\RateResult\Error;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;


/**
 *
 */
class EnvioRapido extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'envio_rapido';
    /**
     * @var bool
     */
    protected $_isFixed = true;
    /**
     * @var ResultFactory
     */
    protected ResultFactory $_rateResultFactory;
    /**
     * @var MethodFactory
     */
    protected MethodFactory $_rateMethodFactory;
    /**
     * @var ProductRepository
     */
    protected ProductRepository $productRepository;
    /**
     * @var SalableQtyBySku
     */
    protected SalableQtyBySku $salableQuantityDataBySku;

    /**
     * @var TimezoneInterface
     */
    protected $_timezone;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param ProductRepository $productRepository
     * @param SalableQtyBySku $salableQuantityDataBySku
     * @param TimezoneInterface $timezone
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory         $rateErrorFactory,
        LoggerInterface      $logger,
        ResultFactory        $rateResultFactory,
        MethodFactory        $rateMethodFactory,
        ProductRepository    $productRepository,
        SalableQtyBySku      $salableQuantityDataBySku,
        TimezoneInterface    $timezone,
        array                $data = []
    )
    {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->productRepository = $productRepository;
        $this->salableQuantityDataBySku = $salableQuantityDataBySku;
        $this->_timezone = $timezone;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function collectRates(RateRequest $request)
    {
        $salableQtySelectedStock = -1;
        try {
            if (!$this->getConfigFlag('active')) {
                return false;
            }
            $this->_logger->info(__METHOD__);

            // -------------------------- INICIO REGLAS DE NEGOCIO --------------------------
            //TODO Las reglas de negocio dependen en la cantidad existente en cada una de las sources
            //TODO Las reglas NO DEPENDEN DEL SALABLE QUANTITY, preguntar con René ¿Quién esta trabajando ese modulo?
            //TODO Creo que existe una limitante, donde Savar SOLO puede enviar UN SOLO producto. Preguntar
            $cumpleReglasEnvioRapido = true;

            //TODO Eliminar el siguiente foreach, solo fue una regla INVENTADA para que se mostrara el error en el checkout
//            /** @var Magento\Quote\Model\Quote\Item $item */
            foreach ($request->getAllItems() as $item) {
                //$productId = $item->getProductId();
                //$this->productRepository->getById($productId);
                $salable = $this->salableQuantityDataBySku->execute($item->getSku());
                //[0] => [ 'stock_id' => 2, 'stock_name' => 'Perfumerias Unidas', 'qty' => 8000, 'manage_stock' => true ]
                $salableQtySelectedStock = $salable[0]['qty'];
                $this->_logger->info('salableQtySelectedStock: ' . $salableQtySelectedStock);
                if($salableQtySelectedStock > 1000){
                    $cumpleReglasEnvioRapido = true;
                }
            }
            // ------------- FIN REGLAS DE NEGOCIO - $cumpleReglasEnvioRapido -------------

            $shippingPrice = $this->getConfigData('price');

            if ($shippingPrice !== false) {
                $this->_logger->info('Diferente de false');
                if ($cumpleReglasEnvioRapido) {
                    $this->_logger->info('Si cumple reglas');
                    $result = $this->_rateResultFactory->create();

                    $method = $this->_rateMethodFactory->create();

                    $method->setCarrier($this->_code);
                    $method->setCarrierTitle($this->getConfigData('title'));

                    $method->setMethod($this->_code);
                    $method->setMethodTitle($this->getConfigData('name'));
                    $method->setData('delivery_time',$this->getDeliveryTime());

                    if ($request->getFreeShipping() === true) {
                        $shippingPrice = '0.00';
                    }

                    $method->setPrice($shippingPrice);
                    $method->setCost($shippingPrice);
                    $method->setPrice('99.9');
                    $method->setCost('99.9');

                    $result->append($method);
                    return $result;

                } else {
                    $this->_logger->info('NO cumple reglas');
                    return $this->createCarrierErrorMessage($salableQtySelectedStock);
                }
            }
        } catch (\Exception $e) {
            $this->_logger->info($e->getMessage());
            return $this->createCarrierErrorMessage($salableQtySelectedStock);
        }
    }

    /**
     * @param $salableQtySelectedStock
     * @return Error
     */
    private function createCarrierErrorMessage($salableQtySelectedStock){
        $error = $this->_rateErrorFactory->create();
        $error->setCarrier($this->_code);
        $error->setCarrierTitle($this->getConfigData('title'));
        $error->setErrorMessage($salableQtySelectedStock . ' ' . $this->getConfigData('specificerrmsg'));
        return $error;
    }

    /**
     * getAllowedMethods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }

    /**
     * @return array[]
     */
    public function getDeliveryTime(){

        $horaActual = $this->_timezone->date()->format('m/d/y H:i:s');
        $tomorrow = date("m/d/y H:i:s",strtotime($horaActual." + 1 day"));

        $todayNameDay = $this->getDayTranslate($this->_timezone->date()->format('l'));
        $todayDay = $this->_timezone->date()->format('d');
        $todayMonth = $this->getMonthTranslate($this->_timezone->date()->format('n'));

        $tomorrowNameDay = $this->getDayTranslate(date("l",strtotime($tomorrow)));
        $tomorrowDay = date('d',strtotime($tomorrow));
        $tomorrowMonth = $this->getMonthTranslate(date('n',strtotime($tomorrow)));

        if(strtotime($horaActual) >= strtotime("0:00:00") && strtotime($horaActual) < strtotime("14:00:00")) {
            //OPCIÓN 1: Usuario selecciona de 12:00 a 16:00
            $textToShowInFront_Option1 = "Tu pedido llegará HOY $todayNameDay $todayDay de $todayMonth en un rango de 12 a 4pm";
            // La opcion que se deberá elegir en el Address Attribute es today__1200_1600
            $horarios_disponibles_Option1 = "today__1200_1600";

            //OPCIÓN 2: Usuario selecciona de 16:00 a 20:00
            $textToShowInFront_Option2 = "Tu pedido llegará HOY $todayNameDay $todayDay de $todayMonth en un rango de 4 a 8pm";
            // La opcion que se deberá elegir en el Address Attribute es today__1600_2000
            $horarios_disponibles_Option2 = "today__1600_2000";
        }
        elseif(strtotime($horaActual) >= strtotime("14:00:00") && strtotime($horaActual) < strtotime("18:00:00")){
            //OPCIÓN 1: Usuario selecciona de 12:00 a 16:00
            $textToShowInFront_Option1 = "Tu pedido llegará MAÑANA $tomorrowNameDay $tomorrowDay de $tomorrowMonth en un rango de 12 a 4pm";
            // La opcion que se deberá elegir en el Address Attribute es tomorrow__1200_1600
            $horarios_disponibles_Option1 = "tomorrow__1200_1600";

            //OPCIÓN 2: Usuario selecciona de 16:00 a 20:00
            $textToShowInFront_Option2 = "Tu pedido llegará HOY $todayNameDay $todayDay de $todayMonth en un rango de 4 a 8pm";
            // La opcion que se deberá elegir en el Address Attribute es today__1600_2000
            $horarios_disponibles_Option2 = "today__1600_2000";

        }
        else{
            //OPCIÓN 1: Usuario selecciona de 12:00 a 16:00
            $textToShowInFront_Option1 = "Tu pedido llegará MAÑANA $tomorrowNameDay $tomorrowDay de $tomorrowMonth en un rango de 12 a 4pm";
            // La opcion que se deberá elegir en el Address Attribute es tomorrow__1200_1600
            $horarios_disponibles_Option1 = "tomorrow__1200_1600";

            //OPCIÓN 2: Usuario selecciona de 16:00 a 20:00
            $textToShowInFront_Option2 = "Tu pedido llegará MAÑANA $tomorrowNameDay $tomorrowDay de $tomorrowMonth en un rango de 4 a 8pm";
            // La opcion que se deberá elegir en el Address Attribute es tomorrow__1600_2000
            $horarios_disponibles_Option2 = "tomorrow__1600_2000";
        }

        return [
            ['label' => $textToShowInFront_Option1, 'option_value' => $horarios_disponibles_Option1],
            ['label' => $textToShowInFront_Option2, 'option_value' => $horarios_disponibles_Option2]
        ];
    }

    /**
     * @param $day
     * @return string
     */
    public function getDayTranslate($day){
        $result = "";
        switch ($day) {
            case "Sunday":
                $result = "Domingo";
                break;
            case "Monday":
                $result = "Lunes";
                break;
            case "Tuesday":
                $result = "Martes";
                break;
            case "Wednesday":
                $result = "Miércoles";
                break;
            case "Thursday":
                $result = "Jueves";
                break;
            case "Friday":
                $result = "Viernes";
                break;
            case "Saturday":
                $result = "Sábado";
                break;
        }

        return $result;
    }

    /**
     * @param $monthNumber
     * @return string
     */
    public function getMonthTranslate($monthNumber){
        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

        return $meses[$monthNumber-1];
    }
}

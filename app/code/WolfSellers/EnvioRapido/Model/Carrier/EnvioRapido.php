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
use Magento\InventoryApi\Api\GetSourceItemsBySkuInterface;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use WolfSellers\Bopis\Model\ResourceModel\AbstractBopisCollection;
use WolfSellers\AmastyLabel\Helper\DynamicTagRules;

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

    /** @var GetSourceItemsBySkuInterface */
    protected $_sourceItemsBySku;

    /** @var DynamicTagRules */
    protected DynamicTagRules $dynamicTagRules;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param ProductRepository $productRepository
     * @param SalableQtyBySku $salableQuantityDataBySku
     * @param TimezoneInterface $timezone
     * @param GetSourceItemsBySkuInterface $sourceItemsBySku
     * @param DynamicTagRules $dynamicTagRules
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface         $scopeConfig,
        ErrorFactory                 $rateErrorFactory,
        LoggerInterface              $logger,
        ResultFactory                $rateResultFactory,
        MethodFactory                $rateMethodFactory,
        ProductRepository            $productRepository,
        SalableQtyBySku              $salableQuantityDataBySku,
        TimezoneInterface            $timezone,
        GetSourceItemsBySkuInterface $sourceItemsBySku,
        DynamicTagRules              $dynamicTagRules,
        array                        $data = []
    )
    {
        $this->_sourceItemsBySku = $sourceItemsBySku;
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->productRepository = $productRepository;
        $this->salableQuantityDataBySku = $salableQuantityDataBySku;
        $this->_timezone = $timezone;
        $this->dynamicTagRules = $dynamicTagRules;
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

            $cumpleReglasEnvioRapido = true;

            /** @var \Magento\Quote\Model\Quote\Item $item */
            foreach ($request->getAllItems() as $item) {
                // Get the amasty_labels that apply per sku
                $labels = $this->dynamicTagRules->shippingLabelsByProductSku($item->getSku());
                // If the label does not exist, we continue with the next product.
                if (!isset($labels['fast'])) continue;

                $fastLabel = boolval($labels['fast']);

                if (!$fastLabel){
                    $cumpleReglasEnvioRapido = false;
                }
            }

            $shippingPrice = $this->getConfigData('price');

            if ($shippingPrice !== false) {
                if ($cumpleReglasEnvioRapido) {

                    $result = $this->_rateResultFactory->create();
                    $method = $this->_rateMethodFactory->create();

                    $method->setCarrier($this->_code);
                    $method->setCarrierTitle($this->getConfigData('title'));

                    $method->setMethod($this->_code);
                    $method->setMethodTitle($this->getConfigData('name'));
                    $method->setData('delivery_time', $this->getDeliveryTime());

                    $method->setPrice($shippingPrice);
                    $method->setCost($shippingPrice);

                    $result->append($method);
                    return $result;

                } else {
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

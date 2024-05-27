<?php

namespace WolfSellers\Bopis\ViewModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use WolfSellers\AmastyLabel\Helper\DynamicTagRules;
use WolfSellers\Bopis\Helper\RealStates;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use WolfSellers\Bopis\Logger\Logger;
use WolfSellers\OrderQR\Helper\SourceQuantityHelper;

class GeneralOrder implements ArgumentInterface
{
    /**
     * @var DynamicTagRules
     */
    protected $dynamicTagRules;
    /**
     * @var SourceQuantityHelper
     */
    protected $_sourceQuantityHelper;

    /**
     * @param RealStates $_realStates
     * @param RedirectInterface $redirect
     * @param SourceRepositoryInterface $_sourceRepository
     * @param SearchCriteriaBuilder $_searchCriteriaBuilder
     * @param CustomerRepositoryInterface $customerRepository
     * @param OrderRepositoryInterface $orderRepository
     * @param Logger $logger
     * @param DynamicTagRules $dynamicTagRules
     * @param SourceQuantityHelper $sourceQuantityHelper
     */
    public function __construct(
        protected RealStates                  $_realStates,
        protected RedirectInterface           $redirect,
        protected SourceRepositoryInterface   $_sourceRepository,
        protected SearchCriteriaBuilder       $_searchCriteriaBuilder,
        protected CustomerRepositoryInterface $customerRepository,
        protected OrderRepositoryInterface    $orderRepository,
        protected Logger                      $logger,
        DynamicTagRules $dynamicTagRules,
        SourceQuantityHelper $sourceQuantityHelper
    )
    {
        $this->dynamicTagRules = $dynamicTagRules;
        $this->_sourceQuantityHelper = $sourceQuantityHelper;
    }

    /**
     * @param $shippingMethodCode
     * @return string
     */
    public function getShippingMethodTitle($shippingMethodCode)
    {
        return $this->_realStates->getShippingMethodTitle($shippingMethodCode);
    }

    /**
     * @param $status
     * @return string|null
     */
    public function getStateLabel($status): ?string
    {
        if (!$status) {
            return $status;
        }
        return $this->_realStates->getStateLabel($status);
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->redirect->getRefererUrl();
    }

    /**
     * @param $sourceCode
     * @return string
     */
    public function getOrderSourceName($sourceCode): string
    {
        if (!$sourceCode || $sourceCode == '') return '';

        $this->_searchCriteriaBuilder->addFilter('source_code', $sourceCode);
        $searchCriteria = $this->_searchCriteriaBuilder->create();

        $searchCriteriaResult = $this->_sourceRepository->getList($searchCriteria);
        $sources = $searchCriteriaResult->getItems();

        $source = current($sources);

        if (!$source) return $sourceCode;

        return $source->getName();
    }

    /**
     * @param $attributeCode
     * @param $value
     * @return bool|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getRealAddrOptionValue($attributeCode, $value): bool|string
    {
        return $this->_realStates->getRealAddrOptionValue('customer_address', $attributeCode, $value);
    }

    /**
     * @param $horarioDeEntrega
     * @return string
     */
    public function getSchedule($horarioDeEntrega)
    {
        return $this->_realStates->getSchedule($horarioDeEntrega);
    }

    /**
     * @param $horarioDeEntrega
     * @param $createdAt
     * @return array|string
     */
    public function getAllSchedule($horarioDeEntrega, $createdAt)
    {
        $return = [
            'default-msj' => 'Tu pedido llegará en un lapso de 48 horas.',
            'default-instore' => 'Podrás recoger tu pedido en un lapso de 48 horas.'
        ];

        $txt = $this->_realStates->getSchedule($horarioDeEntrega);

        if ($txt == "") return $return;

        $data = explode("de", $txt);

        if (trim($data[0]) == 'Hoy') {
            $date = date('d/m/Y', strtotime($createdAt));
        } else {
            $date = date('d/m/Y', strtotime('+ 24 hours', strtotime($createdAt)));;
        }

        $return['fecha'] = $data[0] . ' ' . $date;
        $return['horario'] = $data[1];

        return $return;
    }

    /**
     * @param $createdAt
     * @param $sourceCode
     * @param $orderId
     * @return string[]
     */
    public function getPickUpSchedule($createdAt, $sourceCode, $orderId){

        $return = [
            'default-instore' => 'Podrás recoger tu pedido en un lapso de 48 horas.',
            'fecha' => '',
            'horario' => ''
        ];

        try {
            $rules = $this->notLabelsApplied($orderId);
            $sourceInfo = $this->_sourceRepository->get($sourceCode);
            $todayName = date("l");
            $currentDelivery = $this->_sourceQuantityHelper->getEstimatedDeliveryDateByOrderId($orderId);
            $date = strtotime($currentDelivery);
            $orderHour = strtotime( date('H:i', $date));
            $defaultEndHour = strtotime('21:30');
            $deliveryAfterDay = date('m/d/Y', strtotime('+ 48 hours', strtotime($createdAt)));

            if ($this->isAvilableDays($currentDelivery, $sourceCode) === false) {
                $deliveryDay = $this->getEstimatedDeliveryAfterDate($currentDelivery, $sourceCode);

                if ($this->isAvilableDays($deliveryDay, $sourceCode) == true) {
                    $date = strtotime($deliveryDay);
                }
            }

            if ($this->isAvilableDays($deliveryAfterDay, $sourceCode) === false) {
                $deliveryAfterDay = $this->getEstimatedDeliveryAfterDate($deliveryAfterDay, $sourceCode);

                if ($this->isAvilableDays($deliveryAfterDay, $sourceCode) == true) {
                    $deliveryAfterDay = $deliveryAfterDay;
                }
            }

            if ($rules['notLabelsApplied']) {
                $return['fecha'] = $rules['delivery_day'];
                $return['horario'] = '08:00 am'.' a '.'09:30 pm';

                return $return;
            }

            if ($sourceInfo->getData('opening_hours') !== null) {
                $schedule = json_decode($sourceInfo->getData('opening_hours'));

                foreach ($schedule as $day){

                    if (strtolower($day->week_day) == strtolower($this->getDay($todayName))) {
                        $start = strtotime( $day->start_time );
                        $end = strtotime( $day->end_time );
                        if ( $orderHour < $end ) {
                            $return['fecha'] = $this->getFormat(date('m/d/Y', $date));
                            $return['horario'] = $day->start_time .' a '.$day->end_time;
                        } else {
                            $return['fecha'] = $this->getFormat($deliveryAfterDay);
                            $return['horario'] = $day->start_time.' a '.$day->end_time;
                        }
                    }
                }
            } else {
                $return['fecha'] = $this->getFormat($deliveryAfterDay);
                $return['horario'] = '08:00 am'.' a '.'09:30 pm';
            }

        }catch (\Exception $exception){
        }

        return $return;
    }

    /**
     * @param $day
     * @return string
     */
    public function getDay($day)
    {
        return match ($day) {
            "Monday" => "Lunes",
            "Tuesday" => "Martes",
            "Wednesday" => "Miércoles",
            "Thursday" => "Jueves",
            "Friday" => "Viernes",
            "Saturday" => "Sábado",
            "Sunday" => "Domingo"
        };
    }

    /**
     * @param $customerId
     * @param bool $whitType
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getCustomerIdentificacion($customerId, bool $whitType = true): string
    {
        $identification_type = $this->getCustomerAttributeValue($customerId, 'identificacion');
        $identification_number = $this->getCustomerAttributeValue($customerId, 'numero_de_identificacion');

        if ($whitType) {
            $type = $this->_realStates->getRealAddrOptionValue('customer', 'identificacion', $identification_type);
            return ($type ? $type . ' - ' : '') . $identification_number;
        }


        return $identification_number;
    }

    /**
     * @param $customerId
     * @param $attr
     * @return mixed|string
     */
    public function getCustomerAttributeValue($customerId, $attr): mixed
    {
        try {
            $customer = $this->customerRepository->getById($customerId);
        } catch (\Throwable $error) {
            $this->logger->error($error->getMessage(), ["you are querying information about a deleted customer", $customerId]);
            return '';
        }

        $attribute = $customer->getCustomAttribute($attr);

        if (!$attribute) return '';

        return $attribute->getValue();
    }

    /**
     * @param $customerId
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function getCustomerData($customerId): bool|\Magento\Customer\Api\Data\CustomerInterface
    {
        try {
            return $this->customerRepository->getById($customerId);
        } catch (\Throwable $error) {
            $this->logger->error($error->getMessage(), ["You are trying to query a deleted client.", $customerId]);
            return false;
        }

    }

    /**
     * @return string
     */
    public function getInStoreCode(): string
    {
        return \WolfSellers\Bopis\Model\ResourceModel\AbstractBopisCollection::PICKUP_SHIPPING_METHOD;
    }

    /**
     * @param $orderId
     * @return array
     * @throws LocalizedException
     */
    public function getCustomerOrderBillingInformation($orderId)
    {
        $order = $this->orderRepository->get($orderId);

        $typeId = $this->_realStates->getRealAddrOptionValue(
            'customer',
            'identificacion',
            $order->getCustomerIdentificacion()
        );

        return [
            'name' => $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname(),
            'email' => $order->getCustomerEmail(),
            'tel' => $order->getCustomerTelefono(),
            'type_id' => $typeId,
            'id_number' => $order->getCustomerNumeroDeIdentificacion()
        ];
    }

    /**
     * @param $orderId
     * @return bool[]
     * @throws NoSuchEntityException
     */
    public function ruleslabelsApplied($orderId){
        $order = $this->orderRepository->get($orderId);
        $items = $order->getItems();
        $fastShipping = false;
        $inStorePickup = false;
        $noRules = false;
        $afterDay = $this->dateAfter($order->getCreatedAt());

        foreach($items as $item){
            $rules = $this->dynamicTagRules->shippingLabelsByProductSku($item->getSku());
            if($rules['fast']== true){
                $fastShipping = true;
            }
            if($rules['instore']==true){
                $inStorePickup= true;
            }
            if($rules['fast']== false && $rules['instore']==false){
                $noRules=true;
            }
        }
        $rules = [
            'fastShipping' => $fastShipping,
            'inStorePickup' => $inStorePickup,
            'noRules' => $noRules,
            'delivery_day'=> $afterDay
        ];
        return $rules;
    }

    /**
     * @param $orderId
     * @return array
     * @throws NoSuchEntityException
     */
    public function notLabelsApplied($orderId){
        $rules = $this->ruleslabelsApplied($orderId);
        $return['notLabelsApplied'] = false;
        $return['delivery_day'] = $rules['delivery_day'];

        if($rules['fastShipping'] == false && $rules['inStorePickup'] == false && $rules['noRules'] == true ){
            $return['notLabelsApplied'] = true;
        }

        return $return;
    }

    /**
     * @param $date
     * @return string
     */
    public function dateAfter($date){
        $after = date('m/d/Y', strtotime('+ 48 hours', strtotime($date)));

        return $this->getFormat($after);
    }

    /**
     * @param $date
     * @return string
     */
    public function getFormat($date){
        $dayName = $this->getDayTranslate(date('l',strtotime($date)));
        $day = date('d',strtotime($date));
        $month = $this->getMonthTranslate(date('n',strtotime($date)));
        return "$dayName $day de $month";
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

    /**
     * @param $deliveryDay
     * @param $sourceCode
     * @return bool
     */
    public function isAvilableDays($deliveryDay, $sourceCode){

        $deliveryDayName = $this->getDayTranslate(date("l", strtotime( $deliveryDay )));
        $result = false;

        foreach ($this->getSourceSchedule($sourceCode) as $day) {
            if (strtolower($day->week_day) == strtolower( $deliveryDayName)) {
                $result= true;
            }
        }

        return $result;
    }

    /**
     * @param $day
     * @param $sourceCode
     * @return false|string
     */
    public function getEstimatedDeliveryAfterDate($day, $sourceCode)
    {
        $afterday = date('Y-m-d',strtotime($day. " +24 hour"));
        $afterdayName = $this->getDayTranslate(date("l", strtotime($afterday)));

        $isAvilable = $this->isAvilableDays($afterday, $sourceCode);
        if ($isAvilable === false) {
            $this->howsNextAvilableDays($afterday, $sourceCode);
            $day = $afterday;
            $afterday = date('Y-m-d',strtotime($day. " +24 hour"));
        }

        return $afterday;
    }

    /**
     * @param $deliveryDay
     * @param $sourceCode
     * @return false|string
     */
    public function howsNextAvilableDays($deliveryDay, $sourceCode)
    {
        return $this->getEstimatedDeliveryAfterDate($deliveryDay, $sourceCode);
    }

    /**
     * @param $sourceCode
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getSourceSchedule($sourceCode)
    {
        $sourceInfo = $this->_sourceRepository->get($sourceCode);

        return json_decode($sourceInfo->getData('opening_hours'));
    }

}

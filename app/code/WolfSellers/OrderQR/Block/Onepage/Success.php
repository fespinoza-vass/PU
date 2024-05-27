<?php

namespace WolfSellers\OrderQR\Block\Onepage;

use Magento\Customer\Model\Context;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\Sales\Model\Order;
use WolfSellers\OrderQR\Helper\QR;
use WolfSellers\OrderQR\Helper\SourceQuantityHelper;
use WolfSellers\AmastyLabel\Helper\DynamicTagRules;

class Success extends \Magento\Framework\View\Element\Template
{
    CONST SHIPPING_METHOD_FOR_QRCODE = "instore_pickup";

    /** @var SourceQuantityHelper */
    protected $_sourceQuantityHelper;
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    protected $_orderConfig;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    protected $_qrHelper;

    /**
     * @var DynamicTagRules
     */
    protected DynamicTagRules $dynamicTagRules;

    /**
     * @var SourceRepositoryInterface
     */
    protected $_sourceRepository;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        SourceQuantityHelper $sourceQuantityHelper,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Framework\App\Http\Context $httpContext,
        QR $qrHelper,
        DynamicTagRules $dynamicTagRules,
        SourceRepositoryInterface   $sourceRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_sourceQuantityHelper = $sourceQuantityHelper;
        $this->_qrHelper = $qrHelper;
        $this->_checkoutSession = $checkoutSession;
        $this->_orderConfig = $orderConfig;
        $this->_isScopePrivate = true;
        $this->httpContext = $httpContext;
        $this->dynamicTagRules = $dynamicTagRules;
        $this->_sourceRepository = $sourceRepository;
    }

    /**
     * Render additional order information lines and return result html
     *
     * @return string
     */
    public function getAdditionalInfoHtml()
    {
        return $this->_layout->renderElement('order.success.additional.info');
    }

    /**
     * Initialize data and prepare it for output
     *
     * @return string
     */
    protected function _beforeToHtml()
    {
        $this->prepareBlockData();
        return parent::_beforeToHtml();
    }

    /**
     * Prepares block data
     *
     * @return void
     */
    protected function prepareBlockData()
    {
        $order = $this->_checkoutSession->getLastRealOrder();
        $deliveryDay = $this->_sourceQuantityHelper->getFormat(
            $this->_sourceQuantityHelper->getEstimatedDeliveryDateByOrderId($order->getId())
        );
        $currentDelivery = $this->_sourceQuantityHelper->getEstimatedDeliveryDateByOrderId($order->getId());
        $isAvilableDay = $this->isAvilableDays($currentDelivery);

        if ($isAvilableDay == false) {
            $deliveryDay= '';
            $afterDay = $this->getEstimatedDeliveryAfterDate($currentDelivery);

            if ($this->isAvilableDays($afterDay) == true) {
                $deliveryDay = $this->_sourceQuantityHelper->getFormat($afterDay);
            }
        }

        $this->addData(
            [
                'is_order_visible' => $this->isVisible($order),
                'view_order_url' => $this->getUrl(
                    'sales/order/view/',
                    ['order_id' => $order->getEntityId()]
                ),
                'print_url' => $this->getUrl(
                    'sales/order/print',
                    ['order_id' => $order->getEntityId()]
                ),
                'can_print_order' => $this->isVisible($order),
                'can_view_order'  => $this->canViewOrder($order),
                'order_id'  => $order->getIncrementId(),
                'qr_image'  => $this->_qrHelper->getURLQRImage($order->getEntityId()),
                'is_pickup'  => boolval(($order->getShippingMethod() == self::SHIPPING_METHOD_FOR_QRCODE)),
                'delivery_date' => $deliveryDay
            ]
        );
    }

    /**
     * Is order visible
     *
     * @param Order $order
     * @return bool
     */
    protected function isVisible(Order $order)
    {
        return !in_array(
            $order->getStatus(),
            $this->_orderConfig->getInvisibleOnFrontStatuses()
        );
    }

    /**
     * Can view order
     *
     * @param Order $order
     * @return bool
     */
    protected function canViewOrder(Order $order)
    {
        return $this->httpContext->getValue(Context::CONTEXT_AUTH)
            && $this->isVisible($order);
    }

    /**
     * @return string
     * @since 100.2.0
     */
    public function getContinueUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function ruleslabelsApplied(){
        $order = $this->_checkoutSession->getLastRealOrder();
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
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function notLabelsApplied(){
        $rules = $this->ruleslabelsApplied();
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

        return $this->_sourceQuantityHelper->getFormat($after);
    }

    /**
     * @param $deliveryDay
     * @return bool
     */
    public function isAvilableDays($deliveryDay){

        $deliveryDayName = $this->getDayTranslate(date("l", strtotime( $deliveryDay )));
        $result = false;

        foreach ($this->getSourceSchedule() as $day) {
            if (strtolower($day->week_day) == strtolower( $deliveryDayName)) {
                $result= true;
            }
        }

        return $result;
    }

    /**
     * @param $day
     * @return false|string
     */
    public function getEstimatedDeliveryAfterDate($day)
    {
        $afterday = date('Y-m-d',strtotime($day. " +24 hour"));
        $afterdayName = $this->getDayTranslate(date("l", strtotime($afterday)));

        $isAvilable = $this->isAvilableDays($afterday);
        if ($isAvilable === false) {
            $this->howsNextAvilableDays($afterday);
            $day = $afterday;
            $afterday = date('Y-m-d',strtotime($day. " +24 hour"));
        }

        return $afterday;
    }

    /**
     * @param $deliveryDay
     * @return false|string
     */
    public function howsNextAvilableDays($deliveryDay)
    {
        return $this->getEstimatedDeliveryAfterDate($deliveryDay);
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSourceSchedule()
    {
        $order = $this->_checkoutSession->getLastRealOrder();
        $sourceInfo = $this->_sourceRepository->get($order->getData('source_code'));

        return json_decode($sourceInfo->getData('opening_hours'));
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

}

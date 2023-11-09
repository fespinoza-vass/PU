<?php

namespace WolfSellers\OrderQR\Helper;

use Magento\AsyncOrder\Model\Order;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\InventoryApi\Api\SourceItemRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\OrderRepositoryInterface;



/**
 *
 */
class SourceQuantityHelper extends AbstractHelper
{

    /** @var OrderRepositoryInterface */
    protected $_orderRepository;
    /**
     * @var TimezoneInterface
     */
    protected $_timezone;
    /**
     * @var SourceItemRepositoryInterface
     */
    protected SourceItemRepositoryInterface $sourceItems;

    /**
     * @var SearchCriteriaBuilder
     */
    protected SearchCriteriaBuilder $searchCriteriaBuilder;

    /** @var CartRepositoryInterface */
    protected $_cartRepository;
    /** @var GetSalableQuantityDataBySku */
    protected $_getSalableQuantity;


    /**
     * @param TimezoneInterface $timezone
     * @param CartRepositoryInterface $cartRepository
     * @param GetSalableQuantityDataBySku $getSalableQuantity
     * @param SourceItemRepositoryInterface $sourceItems
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param OrderRepositoryInterface $orderRepository
     * @param Context $context
     */
    public function __construct(
        TimezoneInterface $timezone,
        CartRepositoryInterface $cartRepository,
        GetSalableQuantityDataBySku $getSalableQuantity,
        SourceItemRepositoryInterface $sourceItems,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        OrderRepositoryInterface $orderRepository,
        Context $context
    ) {
        $this->_orderRepository = $orderRepository;
        $this->_timezone = $timezone;
        $this->sourceItems = $sourceItems;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_cartRepository = $cartRepository;
        $this->_getSalableQuantity = $getSalableQuantity;
        parent::__construct($context);
    }

    /**
     * @param $cartId
     * @param $sourceCode
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function hasStockInSourceByCardId($cartId, $sourceCode){
        $cart = $this->_cartRepository->get($cartId);

        $stock = true;

        /** @var CartItemInterface $item */
        foreach($cart->getItems() as $item){
            $qty = $this->getStockBySource($item->getSku(), $sourceCode);
            if($qty <= 0 || ($qty < $item->getQty())){
                $stock = false;
            }
        }
        return $stock;
    }


    /**
     * @param $orderId
     * @param $sourceCode
     * @return bool
     */
    public function hasStockInSourceByOrder($orderId, $sourceCode=null){
        $order = $this->_orderRepository->get($orderId);
        if(!$sourceCode){
            $sourceCode = $order->getExtensionAttributes()->getPickupLocationCode();
            if(!$sourceCode){
                return false;
            }
        }

        $stock = true;

        /** @var OrderItemInterface $item */
        foreach($order->getItems() as $item){
            $qty = $this->getStockBySource($item->getSku(), $sourceCode);
            if($qty <= 0 || ($qty < $item->getQtyOrdered())){
                $stock = false;
            }
        }
        return $stock;
    }

    /**
     * @param $sku
     * @param $sourceCode
     * @return int
     */
    public function getStockBySource($sku, $sourceCode){
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('sku', $sku)
            ->create();
        $sourceItemData = $this->sourceItems->getList($searchCriteria);
        foreach ($sourceItemData->getItems() as $sourceItem){
            if($sourceItem->getSourceCode() == $sourceCode){
                if(!$sourceItem->getStatus() || $sourceItem->getQuantity() <= 0) {
                    return 0;
                }
                return intval($sourceItem->getQuantity());
            }
        }
        return 0;
    }

    /**
     * @param $cartId
     * @param $source
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getEstimatedDeliveryDateByCartId($cartId, $source){
        $currentDate = $this->_timezone->date()->format('Y-m-d');;
        $sourceHasStock = $this->hasStockInSourceByCardId($cartId,$source);

        if($sourceHasStock){
            // hay stock en sucursal, puede recoger el mismo dia
            return $currentDate;
        } else {
            // no hay stock en sucursal
            return date('Y-m-d',strtotime($currentDate. " +24 hour"));
        }
    }

    /**
     * @param $orderId
     * @param $source
     * @return string
     */
    public function getEstimatedDeliveryDateByOrderId($orderId, $source=null){
        $currentDate = $this->_timezone->date()->format('Y-m-d');;
        $sourceHasStock = $this->hasStockInSourceByOrder($orderId,$source);

        if($sourceHasStock){
            // hay stock en sucursal, puede recoger el mismo dia
            return $currentDate;
        } else {
            // no hay stock en sucursal
            return date('Y-m-d',strtotime($currentDate. " +24 hour"));
        }
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
}


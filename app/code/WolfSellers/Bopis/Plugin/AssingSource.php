<?php

namespace WolfSellers\Bopis\Plugin;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Model\OrderFactory;
use Magento\InventoryInStorePickupSales\Model\ResourceModel\OrderPickupLocation\GetPickupLocationCodeByOrderId;
use Magento\Framework\Api\SearchCriteriaBuilder;
use WolfSellers\Bopis\Model\ResourceModel\AbstractBopisCollection;
use WolfSellers\DireccionesTiendas\Api\DireccionesTiendasRepositoryInterface;
use WolfSellers\DireccionesTiendas\Api\Data\DireccionesTiendasInterface;
use WolfSellers\EnvioRapido\Helper\DistrictGeoname;
use WolfSellers\OrderQR\Logger\Logger;

class AssingSource
{

    CONST FAST_SHIPPING_METHOD_CODE = "envio_rapido_envio_rapido";

    CONST REGULAR_SHIPPING_METHOD_CODE = "flatrate_flatrate";

    CONST DEFAULT_LURIN_SOURCE = "1";

    /** @var DistrictGeoname */
    protected $_districtGeoname;
    const PARAM_TO_FILTER = 'colony';

    /**
     * @param GetPickupLocationCodeByOrderId $_getPickupLocationCodeByOrderId
     * @param SearchCriteriaBuilder $_searchCriteriaBuilder
     * @param DireccionesTiendasRepositoryInterface $direccionesTiendasRepository
     * @param Logger $_logger
     */
    public function __construct(
        protected GetPickupLocationCodeByOrderId        $_getPickupLocationCodeByOrderId,
        protected SearchCriteriaBuilder                 $_searchCriteriaBuilder,
        protected DireccionesTiendasRepositoryInterface $direccionesTiendasRepository,
        protected Logger                                $_logger,
        DistrictGeoname                       $districtGeoname

    )
    {
        $this->_districtGeoname = $districtGeoname;
    }

    /**
     * @param OrderManagementInterface $subject
     * @param OrderInterface $result
     * @return OrderInterface
     */
    public function afterPlace(OrderManagementInterface $subject, OrderInterface $result): OrderInterface
    {
        $order = $result;
        try{
            if($order->getShippingMethod() == self::FAST_SHIPPING_METHOD_CODE){
                $this->_districtGeoname->assignSourceToOrder($order);
            }
            if($order->getShippingMethod() == self::REGULAR_SHIPPING_METHOD_CODE){
                $order->setData('source_code',AbstractBopisCollection::DEFAULT_BOPIS_SOURCE_CODE); // almacen lurin
            }
            if($order->getShippingMethod() == AbstractBopisCollection::PICKUP_SHIPPING_METHOD){
                $sourceCode = $order->getExtensionAttributes()->getPickupLocationCode();
                $order->setData('source_code',$sourceCode); // se toma el source id de la orden
            }
        }catch (\Throwable $error){
            $this->_logger->error($error->getMessage());
        }

        return $result;
    }
}

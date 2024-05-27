<?php

namespace WolfSellers\Bopis\Plugin;

use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\InventoryApi\Api\GetSourceItemsBySkuInterface;
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
use WolfSellers\InventoryReservationBySource\Helper\InventoryBySourceHelper;

/**
 *
 */
class AssingSource
{
    /** @var string */
    CONST URBANO_SHIPPING_METHOD_CODE = "urbano";

    /** @var string */
    CONST FAST_SHIPPING_METHOD_CODE = "envio_rapido_envio_rapido";

    /** @var string */
    CONST REGULAR_SHIPPING_METHOD_CODE = "flatrate_flatrate";

    /** @var string */
    CONST FREE_SHIPPING_METHOD_CODE = "freeshipping_freeshipping";

    /** @var string */
    CONST DEFAULT_LURIN_SOURCE = "1";

    /** @var DistrictGeoname */
    protected $_districtGeoname;
    /**
     *
     */
    const PARAM_TO_FILTER = 'colony';

    /** @var GetSourceItemsBySkuInterface */
    protected $_sourceItemsBySku;

    /** @var InventoryBySourceHelper */
    protected $_inventoryBySource;


    /**
     * @param GetPickupLocationCodeByOrderId $_getPickupLocationCodeByOrderId
     * @param SearchCriteriaBuilder $_searchCriteriaBuilder
     * @param DireccionesTiendasRepositoryInterface $direccionesTiendasRepository
     * @param Logger $_logger
     * @param DistrictGeoname $districtGeoname
     * @param GetSourceItemsBySkuInterface $sourceItemsBySku
     * @param InventoryBySourceHelper $inventoryBySourceHelper
     */
    public function __construct(
        protected GetPickupLocationCodeByOrderId        $_getPickupLocationCodeByOrderId,
        protected SearchCriteriaBuilder                 $_searchCriteriaBuilder,
        protected DireccionesTiendasRepositoryInterface $direccionesTiendasRepository,
        protected Logger                                $_logger,
        DistrictGeoname                                 $districtGeoname,
        GetSourceItemsBySkuInterface                    $sourceItemsBySku,
        InventoryBySourceHelper                         $inventoryBySourceHelper
    ) {
        $this->_inventoryBySource = $inventoryBySourceHelper;
        $this->_districtGeoname = $districtGeoname;
        $this->_sourceItemsBySku = $sourceItemsBySku;
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
            if($order->getShippingMethod() == self::REGULAR_SHIPPING_METHOD_CODE ||
                str_contains($order->getShippingMethod(),self::URBANO_SHIPPING_METHOD_CODE) ||
                $order->getShippingMethod() == self::FREE_SHIPPING_METHOD_CODE
            ){
                $order->setData('source_code',AbstractBopisCollection::DEFAULT_BOPIS_SOURCE_CODE); // almacen lurin
            }
            if($order->getShippingMethod() == AbstractBopisCollection::PICKUP_SHIPPING_METHOD){
                $sourceCode = $order->getExtensionAttributes()->getPickupLocationCode();
                $order->setData('source_code',$sourceCode); // se toma el source id de la orden

                $supply = !$this->validateStock($order); // si no hay suficiente stock
                $order->setData('needs_supply_instore', $supply);
            }
        }catch (\Throwable $error){
            $this->_logger->error($error->getMessage());
        }

        return $result;
    }

    /**
     * @param OrderInterface $order
     * @return bool
     */
    public function validateStock(OrderInterface $order){
        $stockAvailable = true;
        $sourceCode = $order->getExtensionAttributes()->getPickupLocationCode();

        foreach ($order->getItems() as $item) {
            $inventory = $this->_sourceItemsBySku->execute($item->getSku());
            /** @var SourceItemInterface $sourceSku */
            foreach ($inventory as $sourceSku) {
                if($sourceSku->getSourceCode() == $sourceCode){
                    $sourceQuantity = $this->_inventoryBySource->getSalableQtyBySource($item->getSku(),$sourceSku);

                    if (!$sourceSku->getStatus()){
                        $stockAvailable = false;
                    }
                    if ($sourceQuantity < $item->getQtyOrdered()) {
                        $stockAvailable = false;
                    }
                    break;
                }
            }
        }
        return $stockAvailable;
    }
}

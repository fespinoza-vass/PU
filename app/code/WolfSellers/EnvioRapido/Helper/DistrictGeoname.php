<?php

namespace WolfSellers\EnvioRapido\Helper;

use AWS\CRT\Log;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\InventoryApi\Api\GetSourceItemsBySkuInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\Inventory\Model\ResourceModel\Source\CollectionFactory as SourceCollectionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\InventoryDistanceBasedSourceSelection\Model\DistanceProvider\Offline\GetDistance;
use Magento\InventoryDistanceBasedSourceSelectionApi\Api\Data\LatLngInterfaceFactory;
use WolfSellers\EnvioRapido\Logger\Logger as SavarLogger;




/**
 *
 */
class DistrictGeoname extends AbstractHelper
{


    /** @var SavarLogger */
    protected $_savarLogger;

    /** @var GetSourceItemsBySkuInterface */
    protected $_sourceItemsBySku;

    /** @var GetDistance  */
    protected $_getDistance;

    /** @var LatLngInterfaceFactory */
    protected $_latLngInterfaceFactory;

    /** @var SourceCollectionFactory */
    protected $_sourceCollectionFactory;
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @param GetSourceItemsBySkuInterface $sourceItemsBySku
     * @param GetDistance $getDistance
     * @param LatLngInterfaceFactory $latLngInterfaceFactory
     * @param SourceCollectionFactory $sourceCollectionFactory
     * @param ResourceConnection $resourceConnection
     * @param Context $context
     */
    public function __construct(
        SavarLogger $savarLogger,
        GetSourceItemsBySkuInterface $sourceItemsBySku,
        GetDistance $getDistance,
        LatLngInterfaceFactory $latLngInterfaceFactory,
        SourceCollectionFactory $sourceCollectionFactory,
        ResourceConnection $resourceConnection,
        Context $context
    ){
        $this->_savarLogger = $savarLogger;
        $this->_getDistance = $getDistance;
        $this->_latLngInterfaceFactory = $latLngInterfaceFactory;
        $this->_sourceCollectionFactory = $sourceCollectionFactory;
        $this->resourceConnection = $resourceConnection;
        $this->_sourceItemsBySku = $sourceItemsBySku;
        parent::__construct($context);
    }

    /**
     * @return array
     */
    public function getDistrictActiveList(): array
    {
        $result = [];
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('inventory_geoname');

        $qry = $connection->select()->from($tableName)
            ->where('district_active = ?', 1)
            ->order(['city ASC']);

        $rows = $connection->fetchAll($qry);
        if ($rows) {
            foreach($rows as $row){
                $result[] = [
                    'value' => $row['entity_id'],
                    'label' => $row['city'],
                ];
            }
        }
        return $result;
    }

    /**
     * @param $districtEntityId
     * @return false|mixed
     */
    public function getDistrictByEntityId($districtEntityId){
        $result = [];
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('inventory_geoname');

        $qry = $connection->select()->from($tableName)
            ->where('entity_id = ?', $districtEntityId);

        $rows = $connection->fetchRow($qry);

        if ($rows) {
            return $rows;
        }
        return false;
    }

    /**
     * @param $districtName
     * @return false|mixed
     */
    public function getDistrictByName($districtName){
        $result = [];
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('inventory_geoname');

        $qry = $connection->select()->from($tableName)
            ->where('city = ?', $districtName)
            ->where('district_active = ?', 1);

        $rows = $connection->fetchRow($qry);

        if ($rows) {
            return $rows;
        }
        return false;
    }

    /**
     * @return false|\Magento\Framework\DataObject[]
     */
    public function getEnvioRapidoSources(){
        $district = array();

        /** @var \Magento\Inventory\Model\ResourceModel\Source\Collection $collection */
        $collection = $this->_sourceCollectionFactory->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('enabled', true)
            ->addFieldToFilter('is_fastshipping_active', true)
            ->addFieldToFilter('range_radius', ['notnull' => 0]);

        if(count($collection->getItems())<=0){
            return false;
        }

        return $collection->getItems();
    }

    /**
     * @param OrderInterface $order
     * @return array|false|void
     */
    public function getNearestWarehouses(OrderInterface $order){
        $sourceResult = array();
        $distritoId = $order->getShippingAddress()->getDistritoEnvioRapido();
        $clientDistrict = $this->getDistrictByName($order->getShippingAddress()->getData('colony'));
        $sources = $this->getEnvioRapidoSources();

        if(!$sources || !$distritoId){
            return false;
        }

        foreach($sources as $source){
            $sourcelatLng = $this->_latLngInterfaceFactory->create(
                [
                    'lat' => (float)$source->getData('latitude'),
                    'lng' => (float)$source->getData('longitude'),
                ]
            );

            $districtlatLng = $this->_latLngInterfaceFactory->create(
                [
                    'lat' => (float)$clientDistrict['latitude'],
                    'lng' => (float)$clientDistrict['longitude'],
                ]
            );

            $distance = $this->_getDistance->execute($sourcelatLng,$districtlatLng)/1000;

            $this->_savarLogger->info("Distrito: ".$order->getShippingAddress()->getData('colony'). ", ".
                $source->getName() . "Source Distance: ".$distance
            );

            $this->_savarLogger->info("Distrito: ".$order->getShippingAddress()->getData('colony'). ", ".
                $source->getName() . " radio_alcance: ".$source->getData('range_radius')." Source Distance: ".$distance
            );

            if($distance <= $source->getData('range_radius')){

                $this->_savarLogger->info("Distrito: ".$order->getShippingAddress()->getData('colony'). ", ".
                    $source->getName() . " radio_alcance: ".$source->getData('range_radius')." Source Distance: ".$distance.
                    " aplica"
                );

                $stockAvailable = true;
                foreach ($order->getItems() as $item) {
                    $inventory = $this->_sourceItemsBySku->execute($item->getSku());
                    /** @var SourceItemInterface $sourceSku */
                    foreach ($inventory as $sourceSku) {
                        if($sourceSku->getSourceCode() == $source->getSourceCode()){
                            if (!$sourceSku->getStatus()) continue;

                            if ($sourceSku->getQuantity() <= $item->getQtyOrdered()) {
                                $stockAvailable = false;
                            }
                        }
                    }
                }
                if($stockAvailable){
                    $sourceResult[] = $source;
                    $this->_savarLogger->info("Distrito: ".$order->getShippingAddress()->getData('colony'). ", ".
                        $source->getName() . "stock disponible: SI"
                    );
                }else{
                    $this->_savarLogger->info("Distrito: ".$order->getShippingAddress()->getData('colony'). ", ".
                        $source->getName() . "stock disponible: NO"
                    );
                }
            }

            return $sourceResult;
        }
    }

}

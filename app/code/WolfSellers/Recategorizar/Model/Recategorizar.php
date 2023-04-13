<?php

namespace WolfSellers\Recategorizar\Model;

use Magento\Framework\App\ResourceConnection;
use WolfSellers\Recategorizar\Logger\Logger;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\Exception;
use Magento\Indexer\Model\IndexerFactory;
use Magento\Catalog\Model\Indexer\Product\Category\Processor;
use WolfSellers\NotificacionesCron\Model\NotificationEmail\Email;
use WolfSellers\Recategorizar\Helper\Config;


class Recategorizar{

    const TABLE = 'catalog_category_product';
    protected $_resourceCnn;
    protected $_logger;
    protected $_indexerFactory;
    protected $_notification= [];
    protected $_config;



    public function __construct(
        Logger $logger,
        ResourceConnection $resourceCnn,
        IndexerFactory $indexerFactory,
        Config $config        
        )
    {
        $this->_logger = $logger;
        $this->_resourceCnn=$resourceCnn;
        $this->_indexerFactory=$indexerFactory;
        $this->_config=$config;
    }

    public function execute()
    {
        $this->_notification["Inicio"]="Inicia Proceso de Recategorizacion: ".date('d-m-Y H:i:s');

        try{
        $items=$this->getProduct();

        if(count($items)>0){
            $this->deleteCategories();
        }

        foreach($items as $item){
            $this->_logger->info("Product Id:".$item["entity_id"]." con sku: ".$item["sku"]);

            if($item['attribute_code'] == 'categoria'){
                $data=$this->getIdCategorizacion($item,array(2));
                $cat_nivel=array_column($data,'entity_id');
                if(sizeof($cat_nivel)>0){
                    $this->saveCategories($item["entity_id"],$cat_nivel);
                }
            }
            else{
                if(sizeof($cat_nivel)>0){
                    $data=$this->getIdCategorizacion($item,$cat_nivel);
                    $cat_nivel=array_column($data,'entity_id');
                    $this->saveCategories($item["entity_id"],$cat_nivel);
                }
            }
        }
        $this->updatePosition();
        $this->reindexByCode(Processor::INDEXER_ID);
        $this->_notification["Success"]="El proceso finalizo sin errores.";
        $this->_notification["Fin"]="Termina Proceso de Recategorizacion: ".date('d-m-Y H:i:s');
      }
       	catch(\Exception $e){
        $this->_notification["Error"]="Error =>{ ".$e->getMessage()." }";
        $this->_notification["Fin"]="Termina Proceso de Recategorizacion: ".date('d-m-Y H:i:s');
     }
       finally{
       }
    }
    public function getProduct(){
        try{
            $PEV=$this->_resourceCnn->getTableName('catalog_product_entity_varchar');
            $EA=$this->_resourceCnn->getTableName('eav_attribute');
            $CPE =$this->_resourceCnn->getTableName('catalog_product_entity');

            $select=$this->_resourceCnn->getConnection()->select()
                         ->from($CPE,['entity_id','sku'])
                         ->join($PEV, $CPE.'.entity_id='.$PEV.'.row_id',["value"])
                         ->join($EA,$PEV.'.attribute_id='.$EA.'.attribute_id',["attribute_code"])
                         ->where($EA.".attribute_code in ('categoria','sub_categoria','familia')")
                         ->where($PEV.".value IS NOT NULL")
                         ->order($CPE.'.entity_id');
        }
        catch(LocalizedException $e){
            $this->_logger->info("Error en metodo getProduct => { ".$e->getMessage()." }");
        }
        catch(Exception $e){
            $this->_logger->info("Error en metodo getProduct => { ".$e->getMessage()." }");
        }
        return $this->_resourceCnn->getConnection()->fetchAll($select);
    }

    public function getIdCategorizacion($array,$parent){
         $CCE=$this->_resourceCnn->getTableName('catalog_category_entity');
         $CCEV=$this->_resourceCnn->getTableName('catalog_category_entity_varchar');
         $connection  = $this->_resourceCnn->getConnection();

         $value=explode(",",$array['value']);
         $this->_logger->info(json_encode($value));
         try{
                $select= $connection->select()
                ->from($CCE,["entity_id","parent_id"])
                ->join($CCEV,$CCE.'.row_id='.$CCEV.'.row_id',["row_id"])
                ->where($CCEV.".value in (TRIM('".implode("'),TRIM('",$value)."'))")
                ->where($CCE.".parent_id in(".implode(",",$parent).")")
                ->group($CCE.'.entity_id')
                ->group($CCE.'.parent_id')
                ->group($CCEV.'.row_id');
         }
         catch(LocalizedException $e){
            $this->_logger->info("Error en metodo getIdCategorizacion => { ".$e->getMessage()." }");
         }
         catch(Exception $e) {
             $this->_logger->info("Error en metodo getIdCategorizacion => { " . $e->getMessage() . " }");
         }
        return $connection->fetchAll($select);
    }


    public function saveCategories($productId,$array){

        try{
            $connection  = $this->_resourceCnn->getConnection();
            $table =$this->_resourceCnn->getTableName(self::TABLE);

            foreach($array as $category){
                 $data=[
                         "product_id"=>$productId,
                         "category_id"=>$category,
                         "position"=>0];

                 $connection->insert($table,$data);
                 $data=null;
                 }
        }
        catch(LocalizedException $e){
            $this->_logger->info("Error en metodo saveCategories => { ".$e->getMessage()." }");
        }
        catch(Exception $e){
            $this->_logger->info("Error en metodo  saveCategories => { ".$e->getMessage()." }");
        }
    }

    public function deleteCategories(){
        try{
            $connection  = $this->_resourceCnn->getConnection();
            $table=$connection->getTableName(self::TABLE);
            $connection->delete($table);
        }
        catch(LocalizedException $e){
            $this->_logger->info("Error en metodo deleteCategories => { ".$e->getMessage()." }");
        }
        catch(Exception $e){
            $this->_logger->info("Error en metodo deleteCategories => { ".$e->getMessage()." }");
        }

    }

    public function updatePosition(){
        try{
            $connection  = $this->_resourceCnn->getConnection();
            $table=$this->_resourceCnn->getTableName(self::TABLE);

            $select= $connection->select()
            ->from($table,["entity_id","category_id","(ROW_NUMBER() OVER(PARTITION BY category_id) -1) AS position"]);

            $items=$connection->fetchAll($select);

            foreach($items as $item){
                $column = ["position"=>$item["position"]];
                $where = ['entity_id = ?' => (int)$item["entity_id"]];
                $connection->update($table, $column, $where);
            }
        }
        catch(LocalizedException $e){
            $this->_logger->info("Error en metodo updatePosition => { ".$e->getMessage()." }");
        }
        catch(Exception $e){
            $this->_logger->info("Error en metodo updatePosition => { ".$e->getMessage()." }");
        }
    }

    private function reindexByCode($indexId)
    {
        $indexer = $this->_indexerFactory->create()->load($indexId);
        $indexer->reindexAll($indexId);
    }

}

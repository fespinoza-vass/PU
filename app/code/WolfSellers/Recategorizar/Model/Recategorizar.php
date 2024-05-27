<?php

namespace WolfSellers\Recategorizar\Model;

use Magento\Framework\App\ResourceConnection;
use WolfSellers\Recategorizar\Logger\Logger;
use Magento\Indexer\Model\IndexerFactory;
use Magento\Catalog\Model\Indexer\Product\Category\Processor;
use WolfSellers\Recategorizar\Helper\Config;

class Recategorizar
{
    const TABLE = 'catalog_category_product';
    /**
     * @var ResourceConnection
     */
    protected ResourceConnection $resourceCnn;

    /**
     * @var Logger
     */
    protected Logger $logger;

    /**
     * @var IndexerFactory
     */
    protected IndexerFactory $indexerFactory;

    /**
     * @var array
     */
    protected array $notification = [];

    /**
     * @var Config
     */
    protected Config $config;

    /**
     * @param Logger $logger
     * @param ResourceConnection $resourceCnn
     * @param IndexerFactory $indexerFactory
     * @param Config $config
     */
    public function __construct(
        Logger             $logger,
        ResourceConnection $resourceCnn,
        IndexerFactory     $indexerFactory,
        Config             $config
    )
    {
        $this->logger = $logger;
        $this->resourceCnn = $resourceCnn;
        $this->indexerFactory = $indexerFactory;
        $this->config = $config;
    }

    /**
     * @return void
     */
    public function execute(): void
    {
        $this->notification["Inicio"] = "Inicia Proceso de Recategorizacion: " . date('d-m-Y H:i:s');

        try {
            $items = $this->getProduct();

            if (count($items) > 0) {
                $this->deleteCategories();
            }

            foreach ($items as $item) {
                $this->logger->info("Product Id:" . $item["entity_id"] . " con sku: " . $item["sku"]);

                if ($item['attribute_code'] == 'categoria') {
                    $data = $this->getIdCategorizacion($item, array(2));
                    $catNivel = array_column($data, 'entity_id');

                    if (count($catNivel) > 0) {
                        $this->saveCategories($item["entity_id"], $catNivel);
                    }
                } else {
                    if (count($catNivel) > 0) {
                        $data = $this->getIdCategorizacion($item, $catNivel);
                        $catNivel = array_column($data, 'entity_id');
                        $this->saveCategories($item["entity_id"], $catNivel);
                    }
                }
            }

            $this->updatePosition();
            $this->reindexByCode(Processor::INDEXER_ID);
            $this->notification["Success"] = "El proceso finalizo sin errores.";
            $this->notification["Fin"] = "Termina Proceso de Recategorizacion: " . date('d-m-Y H:i:s');
        } catch (\Exception $e) {
            $this->notification["Error"] = "Error =>{ " . $e->getMessage() . " }";
            $this->notification["Fin"] = "Termina Proceso de Recategorizacion: " . date('d-m-Y H:i:s');
        }
    }

    /**
     * @return array
     */
    public function getProduct(): array
    {
        try {
            $pev = $this->resourceCnn->getTableName('catalog_product_entity_varchar');
            $EA = $this->resourceCnn->getTableName('eav_attribute');
            $CPE = $this->resourceCnn->getTableName('catalog_product_entity');

            $select = $this->resourceCnn->getConnection()->select()
                ->from($CPE, ['entity_id', 'sku'])
                ->join($pev, $CPE . '.entity_id=' . $pev . '.row_id', ["value"])
                ->join($EA, $pev . '.attribute_id=' . $EA . '.attribute_id', ["attribute_code"])
                ->where($EA . ".attribute_code in ('categoria','sub_categoria','familia')")
                ->where($pev . ".value IS NOT NULL")
                ->order($CPE . '.entity_id');
        } catch (\Exception $e) {
            $this->logger->info("Error en metodo getProduct => { " . $e->getMessage() . " }");
        }

        return $this->resourceCnn->getConnection()->fetchAll($select);
    }

    /**
     * @param $array
     * @param $parent
     * @return array
     */
    public function getIdCategorizacion($array, $parent): array
    {
        $CCE = $this->resourceCnn->getTableName('catalog_category_entity');
        $CCEV = $this->resourceCnn->getTableName('catalog_category_entity_varchar');
        $connection = $this->resourceCnn->getConnection();

        $value = explode(",", $array['value']);
        $this->logger->info(json_encode($value));

        try {
            $select = $connection->select()
                ->from($CCE, ["entity_id", "parent_id"])
                ->join($CCEV, $CCE . '.row_id=' . $CCEV . '.row_id', ["row_id"])
                ->where($CCEV . ".value in (TRIM('" . implode("'),TRIM('", $value) . "'))")
                ->where($CCE . ".parent_id in(" . implode(",", $parent) . ")")
                ->group($CCE . '.entity_id')
                ->group($CCE . '.parent_id')
                ->group($CCEV . '.row_id');
        } catch (\Exception $e) {
            $this->logger->info("Error en metodo getIdCategorizacion => { " . $e->getMessage() . " }");
        }

        return $connection->fetchAll($select);
    }


    /**
     * @param $productId
     * @param $array
     * @return void
     */
    public function saveCategories($productId, $array): void
    {
        try {
            $connection = $this->resourceCnn->getConnection();
            $table = $this->resourceCnn->getTableName(self::TABLE);

            foreach ($array as $category) {
                $data = [
                    "product_id" => $productId,
                    "category_id" => $category,
                    "position" => 0];

                $connection->insert($table, $data);
                $data = null;
            }
        } catch (\Exception $e) {
            $this->logger->info("Error en metodo  saveCategories => { " . $e->getMessage() . " }");
        }
    }

    /**
     * @return void
     */
    public function deleteCategories(): void
    {
        try {
            $connection = $this->resourceCnn->getConnection();
            $table = $connection->getTableName(self::TABLE);
            $connection->delete($table);
        } catch (\Exception $e) {
            $this->logger->info("Error en metodo deleteCategories => { " . $e->getMessage() . " }");
        }

    }

    /**
     * @return void
     */
    public function updatePosition(): void
    {
        try {
            $connection = $this->resourceCnn->getConnection();
            $table = $this->resourceCnn->getTableName(self::TABLE);

            $select = $connection->select()
                ->from(
                    $table,
                    ["entity_id", "category_id", "(ROW_NUMBER() OVER(PARTITION BY category_id) -1) AS position"]
                );

            $items = $connection->fetchAll($select);

            foreach ($items as $item) {
                $column = ["position" => $item["position"]];
                $where = ['entity_id = ?' => (int)$item["entity_id"]];
                $connection->update($table, $column, $where);
            }
        } catch (\Exception $e) {
            $this->logger->info("Error en metodo updatePosition => { " . $e->getMessage() . " }");
        }
    }

    /**
     * @param $indexId
     * @return void
     * @throws \Exception
     */
    private function reindexByCode($indexId): void
    {
        $indexer = $this->indexerFactory->create()->load($indexId);
        $indexer->reindexAll($indexId);
    }

}

<?php

namespace WolfSellers\Reports\Ui\DataProvider\Sales;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;

class SalesDataProvider  extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult {

    /**
     * Initialize dependencies.
     *
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param string $mainTable
     * @param string $resourceModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
                      $mainTable = 'sales_order_grid',
                      $resourceModel = \Magento\Sales\Model\ResourceModel\Order::class
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    /**
     * @inheritdoc
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()->joinLeft(
            "sales_order",
            "sales_order.entity_id=main_table.entity_id",
            ["sales_order.status as status_pedido"]
        );

        $this->getSelect()->joinLeft(
            "sales_order_item",
            "sales_order_item.order_id=main_table.entity_id",
            ["sales_order_item.item_id","sales_order_item.sku","sales_order_item.name as sku_description","sales_order_item.qty_ordered","sales_order_item.price", "sales_order_item.base_price","main_table.status as estatus_pedido","main_table.store_name as Purchase_Point"]
        );

        $this->getSelect()->joinLeft(
            "catalog_product_entity",
            "catalog_product_entity.sku=sales_order_item.sku AND catalog_product_entity.entity_id=sales_order_item.product_id"
        );

        $this->getSelect()->joinLeft(
            "catalog_product_entity_varchar",
            "catalog_product_entity_varchar.row_id=catalog_product_entity.row_id AND catalog_product_entity_varchar.attribute_id=844",
            ["value as marca"]
        );
        $this->getSelect()->joinLeft(
            "catalog_product_entity_varchar as cpev2",
            "cpev2.row_id=catalog_product_entity.row_id AND cpev2.attribute_id=742",
            ["value as categoria"]
        );

        $this->getSelect()->joinLeft(
            "braintree_transaction_details",
            "braintree_transaction_details.order_id=main_table.entity_id"
        );
        $this->getSelect()->joinLeft(
            "customer_address_entity",
            "customer_address_entity.entity_id=main_table.customer_id",
            ["customer_address_entity.vat_id as rut","customer_address_entity.firstname","customer_address_entity.lastname"]
        );

        $this->getSelect()->joinLeft(
            "sales_order_address",
            "sales_order_address.parent_id=main_table.entity_id",
            ["sales_order_address.vat_id as dni","sales_order_address.region as region","sales_order_address.city as provincia","sales_order_address.city as city"]
        );
        $this->getSelect()->joinLeft(
            "sales_order_payment",
            "sales_order_payment.parent_id=main_table.entity_id",
            [
                "JSON_VALUE(sales_order_payment.additional_information, '$.PAN') AS TARJETA_NUMBER",
                "SUBSTRING(JSON_VALUE(sales_order_payment.additional_information, '$.PAN'),1,4) AS INI_TARJETA_NUMBER",
                "SUBSTRING(JSON_VALUE(sales_order_payment.additional_information, '$.PAN'),13,16) AS FIN_TARJETA_NUMBER",
                "JSON_VALUE(sales_order_payment.additional_information, '$.brand') AS brand",
                "sales_order_payment.last_trans_id"
            ]
        );
        $this->_logger->info('Query: ' . trim($this->getSelect()->__toString()));

        return $this;
    }
}

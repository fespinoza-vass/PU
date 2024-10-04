<?php

namespace WolfSellers\Reports\Ui\DataProvider\Sales;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;
use WolfSellers\Urbano\Helper\Ubigeo;

class SalesDataProvider  extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult {


    public Ubigeo $ubigeoHelper;
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
        Ubigeo $ubigeoHelper,
        $mainTable = 'sales_order_grid',
        $resourceModel = \Magento\Sales\Model\ResourceModel\Order::class
    ) {
        $this->ubigeoHelper = $ubigeoHelper;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    /**
     * @inheritdoc
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->joinLeft(
            "sales_order_item",
            "sales_order_item.order_id=main_table.entity_id AND sales_order_item.parent_item_id is NULL",
            [
                "sales_order_item.item_id",
                "sales_order_item.sku",
                "sales_order_item.name as sku_description",
                "sales_order_item.qty_ordered",
                "sales_order_item.price",
                "sales_order_item.original_price",
                "sales_order_item.base_price",
                "main_table.status as estatus_pedido",
                "main_table.store_name as Purchase_Point",
                "main_table.grand_total as grand_total",
                "main_table.customer_name as name_customer"
            ]
        );


        $this->getSelect()->columns(new \Zend_Db_Expr("( sales_order_item.original_price - sales_order_item.price) as discount_product"));
//        $this->getSelect()->columns(new \Zend_Db_Expr("DATE_SUB(sales_order_item.created_at, INTERVAL 5 hour ) as purchase_date"));
        $this->getSelect()->columns(new \Zend_Db_Expr("sales_order_item.created_at  as purchase_date"));

        $this->getSelect()->joinLeft(
            "catalog_product_entity",
            "catalog_product_entity.sku=sales_order_item.sku"
        );


        $this->getSelect()->joinLeft(
            "catalog_product_entity_int as cpei",
            "cpei.row_id=catalog_product_entity.row_id AND cpei.attribute_id=247 "
        );

        $this->getSelect()->joinLeft(
            "eav_attribute_option_value as eaov",
            "eaov.option_id=cpei.value",
            ["value as marca"]
        );


        $this->getSelect()->columns("(SELECT GROUP_CONCAT(catalog_category_entity_varchar.value  SEPARATOR ' + ') FROM catalog_category_entity_varchar
        JOIN catalog_category_entity cce ON cce.entity_id = catalog_category_entity_varchar.row_id AND catalog_category_entity_varchar.attribute_id = (
            SELECT attribute_id
            FROM eav_attribute
            WHERE attribute_code = 'name'
              and entity_type_id =
                  (
                      SELECT entity_type_id
                      FROM eav_entity_type
                      WHERE entity_type_code = 'catalog_category'
                  )
        ) AND cce.entity_id in
        (SELECT category_id FROM catalog_category_product where product_id = sales_order_item.product_id) limit 1
    ) as categoria");


        $this->getSelect()->joinLeft(
            "catalog_product_entity_varchar as cpev3",
            "cpev3.row_id=catalog_product_entity.row_id AND cpev3.attribute_id=370",
            ["value as url_1"]
        );

        $this->getSelect()->joinLeft(
            "catalog_product_entity_varchar as cpev4",
            "cpev4.row_id=catalog_product_entity.row_id AND cpev4.attribute_id=376",
            ["concat(cpev4.value,'.html') as url_2"]
        );



        $this->getSelect()->joinLeft(
            "braintree_transaction_details",
            "braintree_transaction_details.order_id=main_table.entity_id"
        );

        $this->getSelect()->joinLeft(
            "customer_address_entity",
            "customer_address_entity.entity_id=main_table.customer_id",
            ["customer_address_entity.firstname","customer_address_entity.lastname"]
        );

        $this->getSelect()->columns(new \Zend_Db_Expr("IF(customer_address_entity.vat_id <> '','FACTURA','BOLETA') as tipopedido"));


        $this->getSelect()->columns(new \Zend_Db_Expr("CONCAT(shipping_information,' ', '150140') as shipping_information"));

        $this->getSelect()->joinLeft(
            "customer_group",
            "customer_group.customer_group_id=main_table.customer_group",
            ["customer_group.customer_group_code as customer_group_code"]
        );

        $this->getSelect()->joinLeft(
            "sales_order_address",
            "sales_order_address.parent_id=main_table.entity_id AND sales_order_address.address_type = 'shipping'",
            [
                "sales_order_address.firstname as nombre_cliente",
                "sales_order_address.lastname as apellido_cliente",
                "sales_order_address.vat_id as dni",
                "sales_order_address.region as region",
                "sales_order_address.city as provincia",
                "company as razon_social",
                "vat_id as ruc"
            ]
        );

        $this->getSelect()->joinLeft(
            new \Zend_Db_Expr("(
                SELECT postcode, MIN(localidad) as localidad
                FROM wolfsellers_zipcode
                GROUP BY postcode
            )"),
            "t.postcode = sales_order_address.postcode",
            [
                "t.localidad as city"
            ]
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

        $this->getSelect()->joinLeft(
            "sales_order",
            "sales_order.entity_id=main_table.entity_id",
            [

                "sales_order.coupon_code",
                "sales_order.discount_amount",
                "IFNULL(sales_order.ubigeo_estimated_delivery,main_table.shipping_information) as urbano_information",
            ]
        );
             
        $this->addFilterToMap('purchase_date', 'sales_order_item.created_at');

        return $this;
    }
}


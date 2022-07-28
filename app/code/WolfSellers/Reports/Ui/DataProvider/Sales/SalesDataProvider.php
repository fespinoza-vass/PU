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

        /*$this->getSelect()->joinInner(
            "sales_order_grid",
            "sales_order_grid.entity_id = main_table.order_id",
            ["increment_id", "customer_name", "shipping_and_handling", "order_status" => "status", "payment_method", "customer_email"]
        );
        $this->getSelect()->joinInner("sales_order_address",
            "sales_order_grid.entity_id = sales_order_address.parent_id and sales_order_address.address_type = 'shipping'",
            ["street", "region","postcode","city"]
        );
        $this->getSelect()->joinInner("catalog_product_entity",
            "catalog_product_entity.entity_id = main_table.product_id",
            "sku as sku-parent"
        );
        $this->addFilterToMap("created_at","sales_order_grid.created_at");
        $this->addExpressionFieldToSelect("created_at", "CONVERT_TZ({{created_at}},'+00:00','-06:00')",['created_at'=>'sales_order_grid.created_at']);
        $this->getSelect()->where("main_table.parent_item_id IS NULL");*/
        return $this;
    }
}

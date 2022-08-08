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
            "braintree_transaction_details",
            "braintree_transaction_details.order_id=main_table.entity_id"
        );
        $this->getSelect()->joinLeft(
            "customer_address_entity",
            "customer_address_entity.entity_id=main_table.customer_id",
            ["customer_address_entity.vat_id as rut"]
        );

        $this->getSelect()->joinLeft(
            "sales_order_address",
            "sales_order_address.parent_id=main_table.entity_id",
            ["sales_order_address.vat_id as dni"]
        )->group('sales_order_address.parent_id');

        $this->getSelect()->joinLeft(
            "sales_order_payment",
            "sales_order_payment.parent_id=main_table.entity_id",
            ["JSON_VALUE(sales_order_payment.additional_information, '$.PAN') AS TARJETA_NUMBER","JSON_VALUE(sales_order_payment.additional_information, '$.brand') AS brand"]
        );

        $this->_logger->info('Query: ' . trim($this->getSelect()->__toString()));

        return $this;
    }
}

<?php

namespace WolfSellers\Bopis\Plugin;

use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Sales\Model\ResourceModel\Order\Grid\Collection as SalesOrderGridCollection;

class CustomSalesOrderGridCollection
{
    private $messageManager;
    private $collection;
    protected  $adminSession;
    protected $logger;

    public function __construct(MessageManager $messageManager,
                                SalesOrderGridCollection $collection,
                                \Magento\Backend\Model\Auth\Session $adminSession,
                                \Psr\Log\LoggerInterface $logger
    ) {

        $this->messageManager = $messageManager;
        $this->collection = $collection;
        $this->adminSession = $adminSession;
        $writer = new \Laminas\Log\Writer\Stream(BP . "/var/log/bopis.log");
        $logger = new \Laminas\Log\Logger();
        $logger->addWriter($writer);
        $this->logger = $logger;
    }

    /**
     * @param $subject
     * @param $collection
     * @param $requestName
     * @return mixed
     */
    public function afterGetReport($subject, $collection, $requestName) {
        if ($requestName !== 'sales_order_grid_data_source') {
            return $collection;
        }
        $storeCode = $this->adminSession->getUser()->getData('source_code');
        $this->logger->debug("admin user");
        $this->logger->debug($this->adminSession->getUser()->getUserName());
        $this->logger->debug($storeCode);

        if ($collection->getMainTable() === $collection->getResource()->getTable('sales_order_grid')) {
            /*
            $orderTable  = $collection->getResource()->getTable('sales_order');
            $quoteTable = $collection->getResource()->getTable('quote');
            $quoteBopisTable = $collection->getResource()->getTable('quote_bopis');

            $collection->getSelect()->joinLeft(
                ['so' => $orderTable],
                "so.entity_id = main_table.entity_id",
                ['quote_id','so.entity_id AS order_id']
            );

            $collection->getSelect()->joinLeft(
                ['q' => $quoteTable],
                "q.entity_id = so.quote_id",
                ['q.entity_id AS q_quote_id']
            );

            $collection->getSelect()->joinLeft(
                ['qb' => $quoteBopisTable],
                "qb.quote_id = so.quote_id",
                ['qb.entity_id AS q_quote_id']
            );

            if (!empty($storeCode) && $storeCode != "all"){
                $collection->getSelect()->where("qb.store = '".$storeCode."'");
            }
            */
            $this->logger->debug("PLUGIN CustomSalesOrderGridCollection ");
            $this->logger->debug($collection->getSelect()->__toString());
        }

        return $collection;

    }



}

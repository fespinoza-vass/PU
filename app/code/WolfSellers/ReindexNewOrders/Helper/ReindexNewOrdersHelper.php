<?php

namespace WolfSellers\ReindexNewOrders\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Indexer\IndexerRegistry;
use WolfSellers\ReindexNewOrders\Logger\Logger as ReindexLogger;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;

class ReindexNewOrdersHelper extends AbstractHelper
{
    /**
     * @var SearchCriteriaBuilder
     */
    protected SearchCriteriaBuilder $_searchCriteriaBuilder;

    /**
     * @var OrderRepositoryInterface
     */
    protected OrderRepositoryInterface $_orderRepository;

    /**
     * @var IndexerRegistry
     */
    protected IndexerRegistry $_indexerRegistry;

    /**
     * @var ReindexLogger
     */
    protected ReindexLogger $_reindexlogger;

    /**
     * @var string
     */
    const XML_PATH_CRON_ENABLED = "reindex_new_orders/cron/enabled";

    /**
     * @var string
     */
    const REINDEX_INPUT_NAME = "reindex_new_order";

    /**
     * @var int
     */
    const FROM_HOURS_AGO = 24;

    /**
     * @param Context $context
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param OrderRepositoryInterface $orderRepository
     * @param IndexerRegistry $indexerRegistry
     * @param ReindexLogger $reindexlogger
     */
    public function __construct(
        Context         $context,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        OrderRepositoryInterface $orderRepository,
        IndexerRegistry $indexerRegistry,
        ReindexLogger $reindexlogger
    )
    {
        parent::__construct($context);
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_orderRepository = $orderRepository;
        $this->_indexerRegistry = $indexerRegistry;
        $this->_reindexlogger = $reindexlogger;
    }

    /**
     * @return bool
     */
    public function isCronEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_CRON_ENABLED);
    }

    /**
     * @param $order
     * @param $indexList
     * @return void
     */
    public function executeReindex($order, $indexList = null): void
    {
        try {
            $productsIds = [];

            if (is_null($indexList)) {
                $indexList = [
                    'inventory',
                    'cataloginventory_stock',
                    'amasty_label',
                ];
            }

            /** @var Order $order */
            foreach ($order->getItems() as $item) {
                $productsIds = [$item->getProductId()];
            }

            foreach ($indexList as $index) {
                $categoryIndexer = $this->_indexerRegistry->get($index);
                $categoryIndexer->reindexList($productsIds);
            }

            $this->_reindexlogger->info('REINDEX: ', [
                'order' => $order->getIncrementId(),
                'items' => $productsIds
            ]);

        } catch (\Throwable $err) {
            $this->_reindexlogger->error($err->getMessage());
        }
    }

    /**
     * @return void
     */
    public function reindexNewOrders(): void
    {
        // story deadline
        $time = date("Y-m-d H:i:s", (time() - (self::FROM_HOURS_AGO * 3600)));

        $searchCriteria = $this->_searchCriteriaBuilder
            ->addFilter('created_at',$time,"gteq")
            ->addFilter('status',["confirmed_order",'processing','pending'],"in")
            ->addFilter(self::REINDEX_INPUT_NAME, 0)
            ->setPageSize(100)
            ->create();

        $orders = $this->_orderRepository->getList($searchCriteria);

        if ($orders->getTotalCount() <= 0){
            return;
        }

        $this->_reindexlogger->info("REINDEXING: " . $orders->getTotalCount() . " orders", ['from' => $time]);

        foreach ($orders->getItems() as $order){
            try {
                $this->executeReindex($order);

                $order->setData(self::REINDEX_INPUT_NAME, 1);
                $this->_orderRepository->save($order);

            }catch (\Throwable $error){
                $this->_reindexlogger->error($error->getMessage(), ['order' => $order->getIncrementId()]);
                continue;
            }
        }
    }
}


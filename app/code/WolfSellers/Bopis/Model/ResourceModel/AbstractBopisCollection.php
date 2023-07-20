<?php


namespace WolfSellers\Bopis\Model\ResourceModel;

use Magento\Backend\Model\Auth\Session as AuthSession;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Sales\Model\ResourceModel\Order\Grid\Collection;
use Psr\Log\LoggerInterface as Logger;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use WolfSellers\Bopis\Helper\Config;

abstract class AbstractBopisCollection extends Collection
{
    private AuthSession $authSession;
    private CookieManagerInterface $cookieManager;


    /**
     * @var Config
     */
    protected $config;

    /**
     * Initialize dependencies.
     *
     * @param CookieManagerInterface $cookieManager
     * @param AuthSession $authSession
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param string $mainTable
     * @param string $resourceModel
     */
    public function __construct(
        CookieManagerInterface $cookieManager,
        AuthSession $authSession,
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        Config $config,
        $mainTable = 'sales_order_grid',
        $resourceModel = \Magento\Sales\Model\ResourceModel\Order::class
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
        $this->authSession = $authSession;
        $this->cookieManager = $cookieManager;
        $this->config = $config;
    }

    /**
     * Get config values
     * 
     * @param String $path
     * @return String|array|int
     */
    public function getConfig($path) {
        return $this->config->getConfig($path);
    }

    protected function _renderFiltersBefore()
    {
        $storeCode = $this->authSession->getUser()->getData('source_code');
        $userType = $this->authSession->getUser()->getData('user_type');
        $websiteId = $this->authSession->getUser()->getData('website_id');
        #$this->_writeLog($storeCode);
        #$this->_writeLog(print_r($this->authSession->getUser()->getData(), true));
        $joinTable = $this->getTable('sales_order');
        $orderTable = $this->getTable('sales_order');
        $quoteTable = $this->getTable('quote');
        $inventorySourceTable = $this->getTable('inventory_source');
        $quoteBopisTable = $this->getTable('quote_bopis');
        #$this->getSelect()->joinLeft($joinTable, 'main_table.entity_id = sales_order.entity_id', ['shipping_method']);
        $this->getSelect()->joinLeft(
            ['so' => $orderTable],
            "so.entity_id = main_table.entity_id",
            [
                'quote_id',
                'so.entity_id AS order_id',
                "IF(so.status = 'complete', so.updated_at, null) as updated_at",
                "IF(so.created_at >= (now() - interval 1 DAY) AND so.status = 'processing',1,0) as is_new"
            ]
        );
        $orderAddressTable  = $this->getTable('sales_order_address');

        $this->getSelect()->joinLeft(
            ['soaShipping' => $orderAddressTable],
            "soaShipping.parent_id = main_table.entity_id AND soaShipping.address_type = 'shipping'",
            ['telephone', 'city', 'postcode', 'street',  'region',
                'customer_address_id'
            ]
        );

      /*  $this->getSelect()->joinInner(
            ['qb' => $quoteBopisTable],
            "qb.quote_id = so.quote_id and qb.type = 'store-pickup'",
            ['qb.entity_id AS q_quote_id']
        );
        */
/*
        $this->getSelect()->joinLeft(
            ['is' => $inventorySourceTable],
            "qb.store = is.source_code",
            ['is.source_code']
        );*/

       // $this->getSelect()->where("so.shipping_method = 'bopis_bopis'");
        if (!empty($storeCode) && $storeCode != "all" && $userType == 1) {
            $this->getSelect()->where("qb.store = '" . $storeCode . "'");
        }
        if ($userType == 2 && is_numeric($websiteId) && $websiteId > 0) {

            $storeCode = $this->cookieManager->getCookie("store_code");
            if (!empty($storeCode) && $storeCode != "all") {
                $this->getSelect()->where("qb.store = '" . $storeCode . "'");
            }

            $storeTable  = $this->getTable('store');
            $this->getSelect()->joinInner(
                ['store' => $storeTable],
                "store.store_id = main_table.store_id AND store.website_id = '$websiteId'",
                ['website_id']
            );

        }
        //die($this->getSelectSql(true))

        parent::_renderFiltersBefore();
    }

    protected function _writeLog($message): void
    {
        $folderPath = BP . "/var/log/";
        $today = date('Y-m-d');
        $writer = new \Laminas\Log\Writer\Stream(BP . "/var/log/bopis.log");
        $logger = new \Laminas\Log\Logger();
        $logger->addWriter($writer);
        $logger->warn($message);
    }
}

<?php


namespace WolfSellers\Bopis\Model\ResourceModel;

use Magento\Backend\Model\Auth\Session as AuthSession;
use Magento\User\Model\ResourceModel\User\CollectionFactory as UserCollectionFactory;
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
    /** @var AuthSession */
    private AuthSession $authSession;

    /** @var CookieManagerInterface */
    private CookieManagerInterface $cookieManager;

    /** @var UserCollectionFactory */
    protected UserCollectionFactory $userCollectionFactory;

    /** @var Config */
    protected $config;

    /** @var string */
    const BOPIS_STORES = 'gestor_bopis_stores';

    /** @var string */
    const BOPIS_FAST_SHIPPING = 'gestor_bopis_fast_shipping';

    /** @var string */
    const BOPIS_REGULAR_SHIPPING = 'gestor_bopis_regular_shipping';

    /** @var string  */
    const FAST_SHIPPING_METHOD = 'flatrate_flatrate';

    /** @var string  */
    const REGULAR_SHIPPING_METHOD = 'urbano';

    /** @var string  */
    const PICKUP_SHIPPING_METHOD = 'instore_instore';

    /**
     * Initialize dependencies.
     *
     * @param CookieManagerInterface $cookieManager
     * @param AuthSession $authSession
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param Config $config
     * @param UserCollectionFactory $userCollectionFactory
     * @param string $mainTable
     * @param string $resourceModel
     */
    public function __construct(
        CookieManagerInterface $cookieManager,
        AuthSession            $authSession,
        EntityFactory          $entityFactory,
        Logger                 $logger,
        FetchStrategy          $fetchStrategy,
        EventManager           $eventManager,
        Config                 $config,
        UserCollectionFactory  $userCollectionFactory,
                               $mainTable = 'sales_order_grid',
                               $resourceModel = \Magento\Sales\Model\ResourceModel\Order::class
    )
    {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
        $this->authSession = $authSession;
        $this->cookieManager = $cookieManager;
        $this->config = $config;
        $this->userCollectionFactory = $userCollectionFactory;
    }

    /**
     * Get config values
     *
     * @param String $path
     * @return String|array|int
     */
    public function getConfig($path)
    {
        return $this->config->getConfig($path);
    }

    /**
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        $sourceCode = $this->authSession->getUser()->getData('source_code');
        $userType = $this->authSession->getUser()->getData('user_type');
        $websiteId = $this->authSession->getUser()->getData('website_id');
        $roleName = $this->getUserRole($this->authSession->getUser());

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
                "IF(so.created_at >= (now() - interval 1 DAY) AND so.status = 'processing',1,0) as is_new",
                'so.source_code',
                'so.shipping_method'
            ]
        );
        $orderAddressTable = $this->getTable('sales_order_address');

        $this->getSelect()->joinLeft(
            ['soaShipping' => $orderAddressTable],
            "soaShipping.parent_id = main_table.entity_id AND soaShipping.address_type = 'shipping'",
            ['telephone', 'city', 'postcode', 'street', 'region',
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

        if ($roleName === self::BOPIS_STORES) {
            $this->getSelect()->where("so.shipping_method = '" . self::PICKUP_SHIPPING_METHOD . "'");

            if (!empty($sourceCode) && $sourceCode != "all") {
                $sql = null;
                $whereList = [];

                $codes = explode(',', $sourceCode);
                foreach ($codes as $code) {
                    $whereList[] = " (so.source_code LIKE '%" . trim($code) . "%') ";
                }

                if (count($whereList)) {
                    $sql = implode(' OR ', $whereList);
                }

                if ($sql) $this->getSelect()->where($sql);
            }
        }

        if ($roleName === self::BOPIS_FAST_SHIPPING) {
            $this->getSelect()->where("so.shipping_method = '" . self::FAST_SHIPPING_METHOD . "'");
        }

        if ($roleName === self::BOPIS_REGULAR_SHIPPING){
            $this->getSelect()->where("so.shipping_method LIKE '" . self::REGULAR_SHIPPING_METHOD . "%'");
        }

        if ($userType == 2 && is_numeric($websiteId) && $websiteId > 0) {

            $storeCode = $this->cookieManager->getCookie("store_code");
            if (!empty($storeCode) && $storeCode != "all") {
                $this->getSelect()->where("qb.store = '" . $storeCode . "'");
            }

            $storeTable = $this->getTable('store');
            $this->getSelect()->joinInner(
                ['store' => $storeTable],
                "store.store_id = main_table.store_id AND store.website_id = '$websiteId'",
                ['website_id']
            );

        }
        //die($this->getSelectSql(true));

        parent::_renderFiltersBefore();
    }

    /**
     * @param $message
     * @return void
     */
    protected function _writeLog($message): void
    {
        $folderPath = BP . "/var/log/";
        $today = date('Y-m-d');
        $writer = new \Laminas\Log\Writer\Stream(BP . "/var/log/bopis.log");
        $logger = new \Laminas\Log\Logger();
        $logger->addWriter($writer);
        $logger->warn($message);
    }

    /**
     * @param \Magento\User\Model\User $user
     * @return mixed|null
     */
    private function getUserRole(\Magento\User\Model\User $user)
    {
        $collection = $this->userCollectionFactory->create();
        $collection->addFieldToFilter('main_table.user_id', $user->getId());
        $userData = $collection->getFirstItem();
        return $userData->getDataByKey('role_name');
    }
}

<?php

namespace WolfSellers\BackendBopis\Block\Adminhtml;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

class Index extends \Magento\Backend\Block\Template
{
    private OrderFactory $orderFactory;
    private \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $collectionFactory;
    private \Magento\Backend\Model\Auth\Session $authSession;
    private \Magento\Framework\App\ResourceConnection $resourceConnection;

    /**
     * @param Context $context
     * @param array $data
     * @param JsonHelper|null $jsonHelper
     * @param DirectoryHelper|null $directoryHelper
     * @param OrderFactory $orderFactory
     * @param CollectionFactory $collectionFactory
     * @param Session $authSession
     * @param ResourceConnection $resourceConnection
     */
    public function __construct
    (
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory  $collectionFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        array $data = [],
        ?JsonHelper $jsonHelper = null,
        ?DirectoryHelper $directoryHelper = null
    )
    {
        parent::__construct($context, $data, $jsonHelper, $directoryHelper);
        $this->orderFactory = $orderFactory;
        $this->collectionFactory = $collectionFactory;
        $this->authSession = $authSession;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @return \Magento\User\Model\User|null
     */
    public function getUser()
    {
        return $this->authSession->getUser();
    }

    public function getNewOrders(){
        $connection = $this->resourceConnection->getConnection();
        $query = "SELECT COUNT(*) FROM sales_order AS so JOIN quote AS q ON q.entity_id = so.quote_id
             JOIN quote_bopis AS qb ON  qb.quote_id = so.quote_id WHERE so.status = '".$this->getConfig('bopis/status/confirmed')."'";
        $result = $connection->fetchAll($query);
        return $result[0]['COUNT(*)'];
    }

    public function getProcessingOrders(){
        $connection = $this->resourceConnection->getConnection();
        $query = "SELECT COUNT(*) FROM sales_order AS so JOIN quote AS q ON q.entity_id = so.quote_id
             JOIN quote_bopis AS qb ON  qb.quote_id = so.quote_id WHERE so.status = '".$this->getConfig('bopis/status/preparing')."'";
        $result = $connection->fetchAll($query);
        return $result[0]['COUNT(*)'];
    }

    public function getCancelOrders(){
        $connection = $this->resourceConnection->getConnection();
        $query = "SELECT COUNT(*) FROM sales_order AS so JOIN quote AS q ON q.entity_id = so.quote_id
             JOIN quote_bopis AS qb ON  qb.quote_id = so.quote_id WHERE so.status = 'canceled'";
        $result = $connection->fetchAll($query);
        return $result[0]['COUNT(*)'];
    }

    public function getHoldedOrders(){
        $connection = $this->resourceConnection->getConnection();
        $query = "SELECT COUNT(*) FROM sales_order AS so JOIN quote AS q ON q.entity_id = so.quote_id
             JOIN quote_bopis AS qb ON  qb.quote_id = so.quote_id WHERE so.status = 'holded'";
        $result = $connection->fetchAll($query);
        return $result[0]['COUNT(*)'];
    }

    public function getCompleteOrders(){
        $connection = $this->resourceConnection->getConnection();
        $query = "SELECT COUNT(*) FROM sales_order AS so JOIN quote AS q ON q.entity_id = so.quote_id
             JOIN quote_bopis AS qb ON  qb.quote_id = so.quote_id WHERE so.status = '".$this->getConfig('bopis/status/complete')."'";
        $result = $connection->fetchAll($query);
        return $result[0]['COUNT(*)'];
    }

    public function getShippingOrders(){
        $connection = $this->resourceConnection->getConnection();
        $query = "SELECT COUNT(*) FROM sales_order AS so JOIN quote AS q ON q.entity_id = so.quote_id
             JOIN quote_bopis AS qb ON  qb.quote_id = so.quote_id WHERE so.status = '".$this->getConfig('bopis/status/shipping')."'";
        $result = $connection->fetchAll($query);
        return $result[0]['COUNT(*)'];
    }

    

}

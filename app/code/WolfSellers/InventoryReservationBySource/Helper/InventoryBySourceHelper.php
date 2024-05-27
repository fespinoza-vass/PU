<?php

namespace WolfSellers\InventoryReservationBySource\Helper;

use Elasticsearch\Endpoints\Get;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\OrderFactory;
use Magento\InventoryApi\Api\GetSourceItemsBySkuInterface;
use Magento\InventoryApi\Api\Data\SourceItemInterface;


/**
 *
 */
class InventoryBySourceHelper extends AbstractHelper
{

    /** @var GetSourceItemsBySkuInterface */
    protected $_sourceItemsBySku;

    /** @var OrderFactory */
    protected $orderFactory;

    /** @var OrderRepositoryInterface */
    protected $_orderRepository;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;


    /**
     * @param Context $context
     * @param ResourceConnection $resourceConnection
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderFactory $orderFactory
     * @param GetSourceItemsBySkuInterface $sourceItemsBySku
     */
    public function __construct(
        Context $context,
        ResourceConnection $resourceConnection,
        OrderRepositoryInterface $orderRepository,
        OrderFactory $orderFactory,
        GetSourceItemsBySkuInterface $sourceItemsBySku
    ){
        $this->_sourceItemsBySku = $sourceItemsBySku;
        $this->orderFactory = $orderFactory;
        $this->_orderRepository = $orderRepository;
        $this->resourceConnection = $resourceConnection;
        parent::__construct($context);
    }

    /**
     * @return void
     */
    public function setSourceCodeInReservation()
    {
        $connect = $this->resourceConnection->getConnection();
        $select = $connect->select()->from('inventory_reservation')->where('source_code = ?', null);
        $reservations = $connect->fetchAll($select);

        foreach($reservations as $reservation){
            $metadata = json_decode($reservation['metadata'],true);
            $incrementalId = $metadata['object_increment_id'];
            $order = $this->orderFactory->create()->loadByIncrementId($incrementalId);
            $sourceCode = $order->getData('source_code');

            $connect->update('inventory_reservation',
                ['source_code'=> $sourceCode],
                ['reservation_id = ?' => (int)$reservation['reservation_id']]
            );
        }
    }

    /**
     * @param string $sku
     * @param int $sourceCode
     * @return float|null
     */
    public function getSalableQtyBySource(string $sku, $source): ?float
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from($this->resourceConnection->getTableName('inventory_reservation'), ['quantity' => 'SUM(quantity)'])
            ->where('sku = ?', $sku)
            ->where('source_code = ?', $source->getSourceCode())
            ->limit(1);

        $reservationQty = $connection->fetchOne($select);

        $reservationQty = $reservationQty ? (float) $reservationQty : 0;

        if($reservationQty != 0){
            return (float) $source->getQuantity() + $reservationQty;
        }else{
            return $source->getQuantity();
        }

        return 0;
    }
}

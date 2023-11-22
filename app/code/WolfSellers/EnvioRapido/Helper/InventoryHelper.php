<?php

namespace WolfSellers\EnvioRapido\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\OrderFactory;


class InventoryHelper extends AbstractHelper
{

    /** @var OrderFactory */
    protected $orderFactory;

    /** @var OrderRepositoryInterface */
    protected $_orderRepository;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;


    public function __construct(
        Context $context,
        ResourceConnection $resourceConnection,
        OrderRepositoryInterface $orderRepository,
        OrderFactory $orderFactory
    ){
        $this->orderFactory = $orderFactory;
        $this->_orderRepository = $orderRepository;
        $this->resourceConnection = $resourceConnection;
        parent::__construct($context);
    }

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
     * @param int $stockId
     * @return null|float
     */
    public function getQtyBySource(string $sku, int $sourceCode): ?float
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from($this->resourceConnection->getTableName('inventory_reservation'), ['quantity' => 'SUM(quantity)'])
            ->where('sku = ?', $sku)
            ->where('source_code = ?', $sourceCode)
            ->limit(1);

        $reservationQty = $connection->fetchOne($select);

        return $reservationQty ? (float) $reservationQty : 0;
    }

}

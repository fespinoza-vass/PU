<?php

namespace WolfSellers\Bopis\Plugin;

use Magento\Framework\App\ResourceConnection;
use Magento\InventoryShipping\Model\ResourceModel\ShipmentSource\SaveShipmentSource;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;

class DuplicateSourceCodes
{

    /** @var string */
    const SOURCE_CODE = 'source_code';

    /**
     * @param ResourceConnection $resourceConnection
     * @param ShipmentRepositoryInterface $shipmentRepository
     * @param OrderRepositoryInterface $orderRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        protected ResourceConnection          $resourceConnection,
        protected ShipmentRepositoryInterface $shipmentRepository,
        protected OrderRepositoryInterface    $orderRepository,
        protected LoggerInterface             $logger
    )
    {
    }

    /**
     * @param SaveShipmentSource $subject
     * @param null $result
     * @param int $shipmentId
     * @param string $sourceCode
     * @return void
     */
    public function afterExecute(SaveShipmentSource $subject, $result, int $shipmentId, string $sourceCode): void
    {
        try {
            $shipment = $this->shipmentRepository->get($shipmentId);
            $orderId = $shipment->getOrderId();
            $order = $this->orderRepository->get($orderId);

            $codes = $order->getData(self::SOURCE_CODE);

            $newCodes = $sourceCode;

            if (!is_null($codes) && !str_contains($codes, $sourceCode)) {
                $newCodes = $codes . ',' . $sourceCode;
            }

            $order->setData(self::SOURCE_CODE, $newCodes);
            $this->orderRepository->save($order);

        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
        }
    }
}

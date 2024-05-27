<?php

namespace WolfSellers\ReindexNewOrders\Plugin;

use WolfSellers\EnvioRapido\Helper\SavarHelper;
use WolfSellers\ReindexNewOrders\Helper\ReindexNewOrdersHelper;
use WolfSellers\ReindexNewOrders\Logger\Logger as ReindexLogger;

class ReindexOrderItems
{
    /**
     * @var ReindexNewOrdersHelper
     */
    protected ReindexNewOrdersHelper $_reindexNewOrdersHelper;

    /**
     * @var ReindexLogger
     */
    protected ReindexLogger $_reindexLogger;

    /**
     * @param ReindexNewOrdersHelper $reindexNewOrdersHelper
     * @param ReindexLogger $reindexLogger
     */
    public function __construct(
        ReindexNewOrdersHelper $reindexNewOrdersHelper,
        ReindexLogger          $reindexLogger
    )
    {
        $this->_reindexNewOrdersHelper = $reindexNewOrdersHelper;
        $this->_reindexLogger = $reindexLogger;
    }

    /**
     * @param SavarHelper $subject
     * @param bool $result
     * @param $order
     * @return bool
     */
    public function afterGenerateShipment(SavarHelper $subject, bool $result, $order): bool
    {
        try {
            if ($result) {
                $this->_reindexLogger->info('Reindex by ship: ' . $order->getIncrementId());
                $this->_reindexNewOrdersHelper->executeReindex($order);
            }
        } catch (\Throwable $e) {
            $this->_reindexLogger->error('REINDEX: ' . $e->getMessage(), ['order' => $order->getIncrementId()]);
        }

        return $result;
    }
}

<?php

namespace WolfSellers\ReindexNewOrders\Cron;

use WolfSellers\ReindexNewOrders\Helper\ReindexNewOrdersHelper;

class ReindexNewOrdersCron
{
    /**
     * @param ReindexNewOrdersHelper $_refreshIndexesHelper
     */
    public function __construct(
        protected ReindexNewOrdersHelper $_refreshIndexesHelper
    )
    {
    }

    /**
     * @return void
     */
    public function execute()
    {
        $enabled = $this->_refreshIndexesHelper->isCronEnabled();

        if (!$enabled) {
            return;
        }

        $this->_refreshIndexesHelper->reindexNewOrders();
    }
}


<?php

declare(strict_types=1);

namespace WolfSellers\Bopis\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Bopis extends AbstractDb
{

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('quote_bopis', 'entity_id');
    }
}


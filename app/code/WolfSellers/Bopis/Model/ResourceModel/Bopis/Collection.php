<?php

declare(strict_types=1);

namespace WolfSellers\Bopis\Model\ResourceModel\Bopis;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use WolfSellers\Bopis\Model\ResourceModel\Bopis;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'entity_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(\WolfSellers\Bopis\Model\Bopis::class, Bopis::class);
    }
}


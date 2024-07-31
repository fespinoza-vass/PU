<?php

namespace WolfSellers\SkinCare\Model\ResourceModel\Simulator;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use WolfSellers\SkinCare\Model\ResourceModel\Simulator;

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
        $this->_init(\WolfSellers\SkinCare\Model\Simulator::class, Simulator::class);
    }
}

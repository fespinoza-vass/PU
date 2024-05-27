<?php

namespace WolfSellers\SkinCare\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Simulator extends AbstractDb
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('wolfsellers_simulator_results', 'entity_id');
    }

}

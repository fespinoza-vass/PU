<?php

namespace WolfSellers\Bopis\Model\ResourceModel\ReadyForPickup\Grid;

use WolfSellers\Bopis\Model\ResourceModel\AbstractBopisCollection;

class Collection extends AbstractBopisCollection
{

    /**
     * @return void
     */
    protected function _renderFiltersBefore(): void
    {
        parent::_renderFiltersBefore();

        $this->getSelect()->where("so.status = '".$this->getConfig('bopis/status/readyforpickup')."'");
        $this->_writeLog($this->getSelect()->__toString());

    }
}

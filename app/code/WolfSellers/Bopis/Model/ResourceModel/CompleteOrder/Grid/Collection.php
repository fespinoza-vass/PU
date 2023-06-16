<?php

namespace WolfSellers\Bopis\Model\ResourceModel\CompleteOrder\Grid;

use WolfSellers\Bopis\Model\ResourceModel\AbstractBopisCollection;

class Collection extends AbstractBopisCollection
{

    protected function _renderFiltersBefore()
    {
        parent::_renderFiltersBefore();

        $this->getSelect()->where("so.status = '".$this->getConfig('bopis/status/complete')."'");
        $this->_writeLog($this->getSelect()->__toString());

    }
}

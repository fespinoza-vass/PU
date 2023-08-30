<?php

namespace WolfSellers\Bopis\Model\ResourceModel\CanceledOrder\Grid;

use WolfSellers\Bopis\Model\ResourceModel\AbstractBopisCollection;

class Collection extends AbstractBopisCollection
{

    protected function _renderFiltersBefore()
    {
        parent::_renderFiltersBefore();

        $this->getSelect()->where("so.status = 'canceled'");
        $this->_writeLog($this->getSelect()->__toString());

    }
}

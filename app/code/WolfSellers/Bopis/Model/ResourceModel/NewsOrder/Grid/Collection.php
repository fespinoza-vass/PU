<?php

namespace WolfSellers\Bopis\Model\ResourceModel\NewsOrder\Grid;


use WolfSellers\Bopis\Model\ResourceModel\AbstractBopisCollection;

class Collection extends AbstractBopisCollection
{

    protected function _renderFiltersBefore()
    {

        parent::_renderFiltersBefore();

        $this->getSelect()->where("so.status = '".$this->getConfig('bopis/status/confirmed')."'");
        $this->_writeLog($this->getSelect()->__toString());
    }
}

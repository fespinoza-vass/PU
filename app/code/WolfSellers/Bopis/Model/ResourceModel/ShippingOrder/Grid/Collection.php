<?php

namespace WolfSellers\Bopis\Model\ResourceModel\ShippingOrder\Grid;

use WolfSellers\Bopis\Model\ResourceModel\AbstractBopisCollection;

/**
 * @class Collection
 */
class Collection extends AbstractBopisCollection
{
    /**
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        parent::_renderFiltersBefore();

        $this->getSelect()->where("so.status = '".$this->getConfig('bopis/status/shipping')."'");
        $this->_writeLog($this->getSelect()->__toString());
    }
}

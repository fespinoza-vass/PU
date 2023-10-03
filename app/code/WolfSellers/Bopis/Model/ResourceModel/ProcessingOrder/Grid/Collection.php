<?php

namespace WolfSellers\Bopis\Model\ResourceModel\ProcessingOrder\Grid;

use WolfSellers\Bopis\Model\ResourceModel\AbstractBopisCollection;

/**
 * @class Collection
 */
class Collection extends AbstractBopisCollection
{
    /**
     * @return void
     */
    protected function _renderFiltersBefore(): void
    {
        parent::_renderFiltersBefore();

        $this->getSelect()->where("so.status = '".$this->getConfig('bopis/status/preparing')."'");
        $this->_writeLog($this->getSelect()->__toString());
    }
}

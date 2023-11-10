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
    protected function _initSelect(): void
    {
        $this->addFilterToMap('entregar_a',
            new \Zend_Db_Expr("IF (so.shipping_method = '" . self::PICKUP_SHIPPING_METHOD . "' ,
                    CONCAT(customer.firstname, ' ', customer.lastname) ,main_table.shipping_name)"));
        parent::_initSelect();
    }

    /**
     * @return void
     */
    protected function _renderFiltersBefore(): void
    {
        parent::_renderFiltersBefore();

        $this->getSelect()->where("so.status = '".$this->getConfig('bopis/status/shipping')."'");
        $this->_writeLog($this->getSelect()->__toString());
    }
}

<?php

namespace WolfSellers\Bopis\Model\ResourceModel\CanceledOrder\Grid;

use WolfSellers\Bopis\Model\ResourceModel\AbstractBopisCollection;

class Collection extends AbstractBopisCollection
{
    /**
     * @return void
     */
    protected function _initSelect(): void
    {
        $this->addFilterToMap('entregar_a',
            new \Zend_Db_Expr("IF (so.shipping_method = '" . self::PICKUP_SHIPPING_METHOD . "' ,
                    `main_table`.`customer_name`, main_table.shipping_name)"));
        parent::_initSelect();
    }

    /**
     * @return void
     */
    protected function _renderFiltersBefore(): void
    {
        parent::_renderFiltersBefore();

        $this->getSelect()->where("so.status = 'canceled'");
        $this->_writeLog($this->getSelect()->__toString());

    }
}

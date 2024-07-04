<?php
declare(strict_types=1);

namespace WolfSellers\Consecutive\Model\ResourceModel;

class Sequential extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('wolfsellers_consecutive_sequential', 'sequential_id');
    }
}


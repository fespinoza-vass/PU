<?php
declare(strict_types=1);

namespace WolfSellers\Consecutive\Model\ResourceModel\Consecutive;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'consecutive_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \WolfSellers\Consecutive\Model\Consecutive::class,
            \WolfSellers\Consecutive\Model\ResourceModel\Consecutive::class
        );
    }
}


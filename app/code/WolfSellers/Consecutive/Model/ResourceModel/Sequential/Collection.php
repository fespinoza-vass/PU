<?php
declare(strict_types=1);

namespace WolfSellers\Consecutive\Model\ResourceModel\Sequential;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'sequential_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \WolfSellers\Consecutive\Model\Sequential::class,
            \WolfSellers\Consecutive\Model\ResourceModel\Sequential::class
        );
    }
}


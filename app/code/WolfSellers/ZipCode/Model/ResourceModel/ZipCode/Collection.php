<?php

namespace WolfSellers\ZipCode\Model\ResourceModel\ZipCode;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'zip_id';
    protected $_eventPrefix = 'wolfsellers_zipcode_collection';
    protected $_eventObject = 'zipcode_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('WolfSellers\ZipCode\Model\ZipCode', 'WolfSellers\ZipCode\Model\ResourceModel\ZipCode');
    }
}

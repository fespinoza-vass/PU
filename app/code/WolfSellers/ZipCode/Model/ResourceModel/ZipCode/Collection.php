<?php

namespace WolfSellers\ZipCode\Model\ResourceModel\ZipCode;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'zip_id';

    /**
     * @var string
     */
    protected $_eventPrefix = 'wolfsellers_zipcode_collection';

    /**
     * @var string
     */
    protected $_eventObject = 'zipcode_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init('WolfSellers\ZipCode\Model\ZipCode', 'WolfSellers\ZipCode\Model\ResourceModel\ZipCode');
    }
}

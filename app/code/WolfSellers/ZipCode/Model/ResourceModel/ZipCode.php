<?php

namespace WolfSellers\ZipCode\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ZipCode extends AbstractDb
{

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init('wolfsellers_zipcode', 'zip_id');
    }
}

<?php

namespace WolfSellers\ZipCode\Model;

use Magento\Framework\Model\AbstractModel;

class ZipCode extends AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'wolfsellers_zipcode';

    protected $_cacheTag = 'wolfsellers_zipcode';

    protected $_eventPrefix = 'wolfsellers_zipcode';

    protected function _construct()
    {
        $this->_init('WolfSellers\ZipCode\Model\ResourceModel\ZipCode');
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }
}

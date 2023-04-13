<?php

namespace WolfSellers\ZipCode\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class ZipCode extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'wolfsellers_zipcode';

    /**
     * @var string
     */
    protected $_cacheTag = 'wolfsellers_zipcode';

    /**
     * @var string
     */
    protected $_eventPrefix = 'wolfsellers_zipcode';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('WolfSellers\ZipCode\Model\ResourceModel\ZipCode');
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return string[]
     */
    public function getIdentities(): array
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return array
     */
    public function getDefaultValues(): array
    {
        return [];
    }
}

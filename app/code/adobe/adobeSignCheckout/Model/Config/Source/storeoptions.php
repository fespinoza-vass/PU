<?php

namespace adobe\adobeSignCheckout\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Store\Api\StoreRepositoryInterface;

/**
 * Class Profile
 * @package Vendor\Package\Model\Config\Source
 */
class StoreOptions implements OptionSourceInterface
{
    /** @var \Magento\Store\Api\StoreRepositoryInterface $_repository */
    private $_repository;

    /**
     * StoreOptions constructor.
     * @param StoreRepositoryInterface $repository
     */
    public function __construct(
        StoreRepositoryInterface $repository
    ) {
        $this->_repository = $repository;
    }

    /**
     * @return array
     */
    public function toOptionArray() : array
    {
        $stores = $this->_repository->getList();
        $options = [];

        foreach ($stores as $store) {
            $options[] = ['label' => $store->getName() . ' - ' . $store->getId(), 'value' => $store->getId()];
        }
        return $options;
    }
}

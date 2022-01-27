<?php

namespace adobe\adobeSignCheckout\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Store\Api\GroupRepositoryInterface;

/**
 * Class Profile
 * @package Vendor\Package\Model\Config\Source
 */
class StoreGroupOptions implements OptionSourceInterface
{
    /** @var \Magento\Store\Api\GroupRepositoryInterface $_repository */
    private $_repository;

    /**
     * StoreOptions constructor.
     * @param GroupRepositoryInterface $repository
     */
    public function __construct(
        GroupRepositoryInterface $repository
    ) {
        $this->_repository = $repository;
    }

    /**
     * @return array
     */
    public function toOptionArray() : array
    {
        $groups = $this->_repository->getList();
        $options = [];

        foreach ($groups as $group) {
            $options[] = ['label' => $group->getName() . ' - ' . $group->getId(), 'value' => $group->getId()];
        }
        return $options;
    }
}

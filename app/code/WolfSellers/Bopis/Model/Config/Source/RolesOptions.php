<?php

namespace WolfSellers\Bopis\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Authorization\Model\ResourceModel\Role\CollectionFactory;
use Psr\Log\LoggerInterface;

class RolesOptions implements ArrayInterface
{
    /** @var string  */
    const ROLE_TYPE_ATTR = 'role_type';

    /**
     * @param CollectionFactory $collectionFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        protected CollectionFactory $collectionFactory,
        protected LoggerInterface           $logger
    )
    {
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        $result = [];

        $roles = $this->collectionFactory->create()
            ->addFieldToFilter(self::ROLE_TYPE_ATTR, \Magento\Authorization\Model\Acl\Role\Group::ROLE_TYPE)
            ->toOptionArray();

        return $roles ?? $result;
    }
}

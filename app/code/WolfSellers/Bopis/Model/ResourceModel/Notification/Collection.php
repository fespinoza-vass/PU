<?php
/**
 * Copyright © Bopis All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace WolfSellers\Bopis\Model\ResourceModel\Notification;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'notification_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \WolfSellers\Bopis\Model\Notification::class,
            \WolfSellers\Bopis\Model\ResourceModel\Notification::class
        );
    }
}

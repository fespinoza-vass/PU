<?php
/**
 * Copyright © Bopis All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace WolfSellers\Bopis\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Notification extends AbstractDb
{

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('wolfsellers_bopis_notification', 'notification_id');
    }
}

<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace WolfSellers\DireccionesTiendas\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class DireccionesTiendas extends AbstractDb
{

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('wolfsellers_direccionestiendas_direccionestiendas', 'direccionestiendas_id');
    }
}


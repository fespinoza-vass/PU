<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace WolfSellers\DireccionesTiendas\Model\ResourceModel\DireccionesTiendas;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'direccionestiendas_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \WolfSellers\DireccionesTiendas\Model\DireccionesTiendas::class,
            \WolfSellers\DireccionesTiendas\Model\ResourceModel\DireccionesTiendas::class
        );
    }
}


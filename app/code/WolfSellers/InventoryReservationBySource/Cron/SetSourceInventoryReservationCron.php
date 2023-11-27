<?php

namespace WolfSellers\InventoryReservationBySource\Cron;

use WolfSellers\InventoryReservationBySource\Helper\InventoryBySourceHelper;

class SetSourceInventoryReservationCron {

    /** @var InventoryBySourceHelper */
    protected $_inventoryBySourceHelper;

    public function __construct(
        InventoryBySourceHelper $inventoryBySourceHelper
    ){
        $this->_inventoryBySourceHelper = $inventoryBySourceHelper;
    }

    public function execute(){
        $this->_inventoryBySourceHelper->setSourceCodeInReservation();
    }
}

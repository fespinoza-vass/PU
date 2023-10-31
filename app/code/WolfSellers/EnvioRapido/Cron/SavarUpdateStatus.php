<?php

namespace WolfSellers\EnvioRapido\Cron;


use WolfSellers\EnvioRapido\Helper\SavarHelper;
class SavarUpdateStatus {


    /** @var SavarHelper */
    protected $_savarHelper;

    public function __construct(
        SavarHelper $savarHelper
    ) {
        $this->_savarHelper = $savarHelper;
    }

    public function execute(){
        $this->_savarHelper->updateSavarOrders();
    }
}

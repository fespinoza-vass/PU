<?php

namespace WolfSellers\Recategorizar\Cron;

use WolfSellers\Recategorizar\Helper\Config;
use WolfSellers\Recategorizar\Model\Recategorizar;
use WolfSellers\Recategorizar\Logger\Logger;


class LoadCron {

    protected $_recategorizar;
    protected $_config;
    protected $_logger;
    protected $_notification = [];

    public function __construct(
        Config $config,
        Recategorizar $recategorizar,
        Logger $logger) {

            $this->_config=$config;
            $this->_recategorizar =$recategorizar;
            $this->_logger=$logger;

    }

    public function execute(){

        if($this->_config->getEnabledCron()){
            $this->_recategorizar->execute();
        }
        else{
            $this->_logger->info("El Cron para recategorizar esta deshabilitado desde el administrador. ".date("Y-m-d H:i:s"));
        }
    }
}

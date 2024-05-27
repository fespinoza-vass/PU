<?php

namespace WolfSellers\Recategorizar\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper {

const XPATH_STATUS_CRON="cron_recategorizar/setting/active";


private ScopeConfigInterface $_scopeConfig;


public function __construct(
    Context $context,
    ScopeConfigInterface $scopeConfig
) {
    $this->_scopeConfig = $scopeConfig;
    parent::__construct($context);
}


public function getEnabledCron(){
    return $this->_scopeConfig->getValue(self::XPATH_STATUS_CRON, ScopeInterface::SCOPE_STORE);
}

}

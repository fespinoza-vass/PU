<?php

namespace WolfSellers\EnvioRapido\Cron;


use Magento\Framework\Config\Scope;
use Magento\Store\Model\ScopeInterface;
use WolfSellers\EnvioRapido\Helper\SavarHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 *
 */
class SavarUpdateStatus {

    CONST XML_PATH_IS_ACTIVE_SAVAR_CRON = "bopis/savar/is_active";

    /** @var ScopeConfigInterface */
    protected $_scopeConfig;

    /** @var SavarHelper */
    protected $_savarHelper;

    /**
     * @param SavarHelper $savarHelper
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        SavarHelper $savarHelper
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_savarHelper = $savarHelper;
    }

    /**
     * @return void
     */
    public function execute(){

        $isSavarCronActive = $this->_scopeConfig->getValue(self::XML_PATH_IS_ACTIVE_SAVAR_CRON,ScopeInterface::SCOPE_STORE);

        if(!$isSavarCronActive){
            return;
        }

        $this->_savarHelper->updateSavarOrders();
    }
}

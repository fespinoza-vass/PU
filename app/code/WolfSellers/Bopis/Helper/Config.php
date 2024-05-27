<?php

namespace WolfSellers\Bopis\Helper;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper {
    const XPATH_IS_ACTIVE = 'bopis/general/is_active';
    const XPATH_ENABLED_SPLIT_ORDER = 'bopis/general/enabled_split_order';
    const XPATH_HOLD_REASONS = 'bopis/general/hold_reasons';

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $_scopeConfig;
    private \Magento\Checkout\Model\Session $session;

    /**
     * Config constructor.
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param Session $session
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $session
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->session = $session;
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function isActive() {
        return $this->_scopeConfig->getValue(self::XPATH_IS_ACTIVE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function enabledSplitOrder() {
        return $this->_scopeConfig->getValue(self::XPATH_ENABLED_SPLIT_ORDER, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getButtonType(){
        return ($this->isActive() || $this->haveProductBundleInCart())  ? 'button' : 'submit';
        //return 'submit';
    }

    public function haveProductBundleInCart(){
        $quote = $this->session->getQuote();
        foreach ($quote->getAllVisibleItems() AS $item){
            if ($item->getProduct()->getTypeId() == "bundle"){
                return true;
            }
        }
        return false;
    }

    /**
     * Get config values
     * 
     * @param String $path
     * @return String|array|int
     */
    public function getConfig($path) {
        $storeScope = ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue($path, $storeScope);
    }
}

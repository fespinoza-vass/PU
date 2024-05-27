<?php

namespace WolfSellers\EnvioRapido\Model;

use Magento\Framework\App\Config\ScopeConfigInterface as ScopeConfig;
use Magento\Store\Model\ScopeInterface;

/**
 * It's better to use a model class to get Data from configuration [no extends]
 * If you try to use configuration from a Helper in a Cron, cron will fail, because AbstractHelper use FRONTEND::AREA
 */
class Configuration
{

    const XML_PATH = 'carriers/envio_rapido/';
    const SANDBOX_MODE = 'sandbox_mode';
    const SANDBOX_TOKEN = 'sandbox_token';
    const PRODUCTION_TOKEN = 'production_token';
    const PRODUCTION_ORDER_ENDPOINT = 'production_order_endpoint';
    const SANDBOX_ORDER_ENDPOINT = 'sandbox_order_endpoint';
    const PRODUCTION_STATUS_ENDPOINT = 'production_status_endpoint';
    const SANDBOX_STATUS_ENDPOINT = 'sandbox_status_endpoint';

    protected ScopeConfig $scopeConfig;

    /**
     * @param ScopeConfig $scopeConfig
     */
    public function __construct(
        ScopeConfig $scopeConfig
    )
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return mixed
     */
    public function getIsActive()
    {
        return $this->scopeConfig->getValue(self::XML_PATH . 'active', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function isSandboxMode()
    {
        return $this->scopeConfig->getValue(self::XML_PATH . self::SANDBOX_MODE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getSandboxToken()
    {
        return $this->scopeConfig->getValue(self::XML_PATH . self::SANDBOX_TOKEN, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getProductionToken()
    {
        return $this->scopeConfig->getValue(self::XML_PATH . self::PRODUCTION_TOKEN, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getSandboxOrderEndpoint()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH . self::SANDBOX_ORDER_ENDPOINT, ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getProductionOrderEndpoint()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH . self::PRODUCTION_ORDER_ENDPOINT, ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getSandboxStatusEndpoint()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH . self::SANDBOX_STATUS_ENDPOINT, ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getProductionStatusEndpoint()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH . self::PRODUCTION_STATUS_ENDPOINT, ScopeInterface::SCOPE_STORE
        );
    }
}


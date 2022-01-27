<?php

namespace adobe\adobeSignCheckout\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_PREFIX = 'adobesign/';
    const API_ACCESS_POINT  = 'adobesign/general/accesspoint';
    const APPLN_ID  = 'adobesign/general/applnId';
    const CLIENT_SECRET  = 'adobesign/general/clientSecret';
    const REFRESH_TOKEN  = 'adobesign/general/refreshtoken';
    const SENDER_EMAIL  = 'adobesign/general/senderemail';
    const EMAIL_TEMPLATE  = 'adobesign/general/emailTemplate';
    const ROLE  = 'adobesign/general/role';
    const AUTH_METHOD  = 'adobesign/general/authMethod';
    const PASSWORD  = 'adobesign/general/password';
    const JSON  = 'adobesign2/general/json';
    const PROD_CATEGORIES  = 'adobesign/general/prodCats';
    const SHOPS  = 'adobesign/general/shops';
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param \Magento\Framework\App\Helper\Context              $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getGeneralConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_PREFIX . 'general/' . $code, $storeId);
    }

    public function getApiAccessPoint()
    {
        return $this->scopeConfig->getValue(self::API_ACCESS_POINT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getApplicationID()
    {
        return $this->scopeConfig->getValue(self::APPLN_ID, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getClientSecret()
    {
        return $this->scopeConfig->getValue(self::CLIENT_SECRET, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getRefreshToken()
    {
        return $this->scopeConfig->getValue(self::REFRESH_TOKEN, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getSenderEmail()
    {
        return $this->scopeConfig->getValue(self::SENDER_EMAIL, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getEmailTemplate()
    {
        return $this->scopeConfig->getValue(self::EMAIL_TEMPLATE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getRole()
    {
        return $this->scopeConfig->getValue(self::ROLE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getAuthMethod()
    {
        return $this->scopeConfig->getValue(self::AUTH_METHOD, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getJson()
    {
        return $this->scopeConfig->getValue(self::JSON, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getPassword()
    {
        return $this->scopeConfig->getValue(self::PASSWORD, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getProdCategories()
    {
        $list = $this->scopeConfig->getValue(self::PROD_CATEGORIES, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $list !== null ? explode(',', $list) : [];
    }

    public function getShops()
    {
        $shops = $this->scopeConfig->getValue(self::SHOPS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $shops !== null ? explode(',', $shops) : [];
    }

}

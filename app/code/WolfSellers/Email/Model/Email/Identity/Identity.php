<?php

namespace WolfSellers\Email\Model\Email\Identity;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Identity
{
    /**
     * @param ScopeConfigInterface $_scopeConfig
     * @param StoreManagerInterface $_storeManager
     */
    public function __construct(
        protected ScopeConfigInterface $_scopeConfig,
        protected StoreManagerInterface $_storeManager
    ) {
    }

    /**
     * @param $path
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getConfigValue($path): mixed
    {
        return $this->_scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $this->getStore()->getId()
        );
    }

    /**
     * @param $path
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isEmailEnabled($path): bool
    {
        return $this->_scopeConfig->isSetFlag(
            $path,
            ScopeInterface::SCOPE_STORE,
            $this->getStore()->getId()
        );
    }

    /**
     * @return \Magento\Store\Api\Data\StoreInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getStore()
    {
        return $this->_storeManager->getStore();
    }
}

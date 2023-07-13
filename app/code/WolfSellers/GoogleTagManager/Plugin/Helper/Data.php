<?php

namespace WolfSellers\GoogleTagManager\Plugin\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\GoogleTagManager\Helper\Data as Subject;
use Magento\GoogleTagManager\Model\Config\TagManagerConfig;
use Magento\Store\Model\ScopeInterface as Scope;

class Data
{
    private const XML_PATH_ACTIVE = 'google/gtag/analytics4/active';

    /**
     * @var TagManagerConfig
     */
    private TagManagerConfig $tagManagerConfig;

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @param TagManagerConfig $tagManagerConfig
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        TagManagerConfig $tagManagerConfig,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->tagManagerConfig = $tagManagerConfig;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Tag Manager helper plugin
     * The original plugin was disabled because not validate if is active Google Analytics 4 before execute
     * @param Subject $subject
     * @param bool $result
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterIsTagManagerAvailable(Subject $subject, bool $result): bool
    {
        if (!$this->scopeConfig->isSetFlag(self::XML_PATH_ACTIVE, Scope::SCOPE_STORE)) {
            return $result;
        }

        return $this->tagManagerConfig->isTagManagerAvailable() ?? $result;
    }
}

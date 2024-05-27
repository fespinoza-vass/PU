<?php

namespace WolfSellers\Bopis\Plugin;

use Magento\Security\Model\Config;
use Magento\Framework\Stdlib\CookieManagerInterface;
use WolfSellers\Bopis\Helper\RememberMeHelper;
use Psr\Log\LoggerInterface;

class RememberMeAdminLifeTime
{
    /**
     * @param RememberMeHelper $rememberMeHelper
     * @param CookieManagerInterface $cookieManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        protected RememberMeHelper       $rememberMeHelper,
        protected CookieManagerInterface $cookieManager,
        protected LoggerInterface        $logger
    )
    {
    }

    /**
     * @param Config $subject
     * @param int $result
     * @return int
     */
    public function afterGetAdminSessionLifetime(Config $subject, int $result): int
    {
        try {
            if ($this->rememberMeHelper->isAvailable()) {
                $this->logger->info($this->rememberMeHelper->getRememberMeTimeExpiration());
                return $this->rememberMeHelper->getRememberMeTimeExpiration();
            }
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
        }

        return $result;
    }
}

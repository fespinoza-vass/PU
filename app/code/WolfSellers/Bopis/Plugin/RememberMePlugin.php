<?php

namespace WolfSellers\Bopis\Plugin;

use Magento\Backend\Model\Auth\Session;
use Magento\Backend\Model\Auth;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Session\Config\ConfigInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use WolfSellers\Bopis\Helper\RememberMeHelper;
use Psr\Log\LoggerInterface;

class RememberMePlugin
{
    /**
     * @param RememberMeHelper $rememberMeHelper
     * @param ConfigInterface $sessionConfig
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param CookieManagerInterface $cookieManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        protected RememberMeHelper       $rememberMeHelper,
        protected ConfigInterface        $sessionConfig,
        protected CookieMetadataFactory  $cookieMetadataFactory,
        protected CookieManagerInterface $cookieManager,
        protected LoggerInterface        $logger
    )
    {
    }

    /**
     * @param Session $subject
     * @param null $result
     * @return void
     */
    public function afterProlong(Session $subject, $result): void
    {
        try {
            if (!$this->rememberMeHelper->isAvailable()) {
                return;
            }

            /** Days to seconds (days * 24) * 60 (minutes) * 60 (seconds) */
            $timeLife = $this->rememberMeHelper->getRememberMeTimeExpiration();
            $cookieValue = $this->cookieManager->getCookie($subject->getName());

            if ($cookieValue) {
                $subject->setUpdatedAt(time());
                $cookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
                    ->setDuration($timeLife)
                    ->setPath($this->sessionConfig->getCookiePath())
                    ->setDomain($this->sessionConfig->getCookieDomain())
                    ->setSecure($this->sessionConfig->getCookieSecure())
                    ->setHttpOnly($this->sessionConfig->getCookieHttpOnly())
                    ->setSameSite($this->sessionConfig->getCookieSameSite());
                $this->cookieManager->setPublicCookie($subject->getName(), $cookieValue, $cookieMetadata);
            }

            $this->logger->info('Se extendio la vida de la sesion actual', ['seconds' => $timeLife]);

        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
        }
    }
}

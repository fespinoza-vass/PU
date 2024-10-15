<?php
/**
 * @copyright Copyright (c) 2024 VASS
 * @package Vass_AppIntegrations
 * @author VASS Team
 */
declare(strict_types=1);

namespace Vass\AppIntegrations\Observer;

use Exception;
use Magento\Customer\Model\Context;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\App\State;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Psr\Log\LoggerInterface;

class CookieLogged implements ObserverInterface
{
    private const COOKIE_IS_LOGGED_IN = 'isLoggedIn';

    /**
     * Constructor.
     *
     * @param State $state
     * @param HttpContext $httpContext
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param CookieManagerInterface $cookieManager
     * @param SessionManagerInterface $sessionManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly State $state,
        private readonly HttpContext $httpContext,
        private readonly CookieMetadataFactory $cookieMetadataFactory,
        private readonly CookieManagerInterface $cookieManager,
        private readonly SessionManagerInterface $sessionManager,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * Execute observer
     *
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer): static
    {
        try {
            if ($this->state->getAreaCode() != 'frontend') {
                return $this;
            }

            $isLoggedIn = $this->isCustomerLoggedIn();
            $this->updateCookieIsLoggedIn($isLoggedIn);
        } catch (LocalizedException $e) {
            return $this;
        }

        return $this;
    }

    /**
     * Retrieve customer login status
     *
     * @return bool
     */
    private function isCustomerLoggedIn(): bool
    {
        return $this->httpContext->getValue(Context::CONTEXT_AUTH) ?? false;
    }

    /**
     * Update cookie isLoggedIn
     *
     * @param bool $isLoggedIn
     * @return void
     */
    private function updateCookieIsLoggedIn(bool $isLoggedIn): void
    {
        $cookie = $this->cookieMetadataFactory->createPublicCookieMetadata();
        $cookie->setDuration(3600);
        $cookie->setPath($this->sessionManager->getCookiePath());
        $cookie->setDomain($this->sessionManager->getCookieDomain());

        try {
            $this->cookieManager->setPublicCookie(self::COOKIE_IS_LOGGED_IN, $isLoggedIn ? '1' : '0', $cookie);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }
}

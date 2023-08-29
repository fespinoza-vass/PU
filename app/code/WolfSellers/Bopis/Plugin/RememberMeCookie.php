<?php

namespace WolfSellers\Bopis\Plugin;

use Magento\Backend\Model\Auth;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Session\Config\ConfigInterface;
use WolfSellers\Bopis\Helper\RememberMeHelper;
use Psr\Log\LoggerInterface;

class RememberMeCookie
{
    public function __construct(
        protected Session                $session,
        protected RememberMeHelper       $rememberMeHelper,
        protected RequestInterface       $request,
        protected CookieMetadataFactory  $cookieMetadataFactory,
        protected CookieManagerInterface $cookieManager,
        protected ConfigInterface $sessionConfig,
        protected LoggerInterface        $logger
    )
    {
    }

    /**
     * @param Auth $subject
     * @param $result
     * @param $username
     * @param $password
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function afterLogin(Auth $subject, $result, $username, $password): void
    {
        try {
            $cookieMetadata = $this->getCookieMetadata();
            $this->cookieManager->deleteCookie(RememberMeHelper::BOPIS_REMEMBER_ME_COOKIE, $cookieMetadata);

            if (!$this->rememberMeHelper->isEnabled()) {
                return;
            }

            if ($this->session->isLoggedIn()) {
                $rememberMe = (bool) $this->request->getParam('rememberme');
                if ($rememberMe) {
                    $timeLife = $this->rememberMeHelper->getRememberMeTimeExpiration();

                    $cookieMetadata = $this->getCookieMetadata($timeLife);
                    $this->cookieManager->setPublicCookie(RememberMeHelper::BOPIS_REMEMBER_ME_COOKIE, $rememberMe, $cookieMetadata);
                }
            }

        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * @param $timeLife
     * @return \Magento\Framework\Stdlib\Cookie\CookieMetadata|\Magento\Framework\Stdlib\Cookie\PublicCookieMetadata
     */
    public function getCookieMetadata($timeLife = null){
        $metadata =  $this->cookieMetadataFactory->createPublicCookieMetadata()
            ->setPath($this->sessionConfig->getCookiePath())
            ->setDomain($this->sessionConfig->getCookieDomain())
            ->setSecure($this->sessionConfig->getCookieSecure())
            ->setHttpOnly($this->sessionConfig->getCookieHttpOnly())
            ->setSameSite($this->sessionConfig->getCookieSameSite());

        if ($timeLife){
            $metadata->setDuration($timeLife);
        }

        return $metadata;
    }
}

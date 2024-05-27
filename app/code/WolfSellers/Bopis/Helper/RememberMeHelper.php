<?php

namespace WolfSellers\Bopis\Helper;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Backend\Model\Auth\Session as AuthSession;
use Magento\User\Model\ResourceModel\User\CollectionFactory as UserCollectionFactory;
use Magento\Authorization\Model\ResourceModel\Role\CollectionFactory as RolCollectionFactory;

class RememberMeHelper implements ArgumentInterface
{

    /** @var string */
    const XML_PATH_BOPIS_REMEMBERME_ENABLED = 'bopis/remember_me/enabled';

    /** @var string */
    const XML_PATH_BOPIS_REMEMBERME_ROLES = 'bopis/remember_me/roles';

    /** @var string */
    const BOPIS_REMEMBER_ME_COOKIE = 'bopis_remember_me';

    /** @var string días */
    const BOPIS_REMEMBER_ME_COOKIE_TIMELIFE = 30;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param CookieManagerInterface $cookieManager
     */
    public function __construct(
        protected ScopeConfigInterface   $scopeConfig,
        protected CookieManagerInterface $cookieManager,
        protected AuthSession            $authSession,
        protected UserCollectionFactory  $userCollectionFactory,
        protected RolCollectionFactory   $rolCollectionFactory,
    )
    {
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_BOPIS_REMEMBERME_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return float|int
     */
    public function getRememberMeTimeExpiration(): float|int
    {
        return (int)self::BOPIS_REMEMBER_ME_COOKIE_TIMELIFE * 24 * 60 * 60;
    }

    /**
     * @return bool
     */
    public function isAvailable(): bool
    {
        $role = $this->getCurrentUserRole();

        if (!$role) {
            return false;
        }

        if (!$this->validateAvailableRole($role)) {
            return false;
        }

        if ($this->isEnabled() && $this->cookieManager->getCookie(self::BOPIS_REMEMBER_ME_COOKIE)) {
            return true;
        }

        return false;
    }

    /**
     * @return false|mixed|null
     */
    public function getCurrentUserRole(): mixed
    {
        if ($this->authSession->isLoggedIn()) {
            return $this->getUserRole($this->authSession->getUser());
        }
        return false;
    }

    /**
     * @param \Magento\User\Model\User $user
     * @return mixed
     */
    private function getUserRole(\Magento\User\Model\User $user): mixed
    {
        $collection = $this->userCollectionFactory->create();
        $collection->addFieldToFilter('main_table.user_id', $user->getId());
        $userData = $collection->getFirstItem();
        return $userData->getDataByKey('role_name');
    }

    /**
     * @param mixed $role
     * @return bool
     */
    private function validateAvailableRole(mixed $role)
    {
        $availableRoles = $this->scopeConfig->getValue(
            self::XML_PATH_BOPIS_REMEMBERME_ROLES,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (!$availableRoles) {
            return true;
        }

        $availableRoles = explode(',', $availableRoles);

        $collection = $this->rolCollectionFactory->create()
            ->addFieldToSelect('role_name')
            ->addFieldToFilter('role_id', $availableRoles);

        foreach ($collection as $rol) {
            if ($rol->getRoleName() == $role) {
                return true;
            }
        }
        return false;
    }

}

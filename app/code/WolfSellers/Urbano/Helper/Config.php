<?php
/**
 * Created by.
 *
 * User: Juan Carlos Hdez <juanhernandez@wolfsellers.com>
 * Date: 2022-03-09
 * Time: 12:45
 */

declare(strict_types=1);

namespace WolfSellers\Urbano\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use WolfSellers\Urbano\Model\Carrier\Urbano;

/**
 * Carrier Config.
 */
class Config extends AbstractHelper
{
    private const FIELD_SANDBOX_MODE = 'sandbox_mode';
    private const FIELD_USER = 'user';
    private const FIELD_PASSWORD = 'password';
    private const FIELD_PRODUCTION_WS_URL = 'production_ws_url';
    private const FIELD_SANDBOX_WS_URL = 'sandbox_ws_url';
    private const FIELD_PACKAGE_TYPE = 'package_type';
    private const FIELD_SSL_VERIFY = 'ssl_verify';

    /**
     * Is sandbox.
     *
     * @return bool
     */
    public function isSandbox(): bool
    {
        return (bool) $this->getValue(self::FIELD_SANDBOX_MODE);
    }

    /**
     * Get user.
     *
     * @return string
     */
    public function getUser(): string
    {
        return $this->getValue(self::FIELD_USER);
    }

    /**
     * Get password.
     *
     * @return string
     */
    public function getPassword(): string
    {
        return $this->getValue(self::FIELD_PASSWORD);
    }

    /**
     * Get production ws url.
     *
     * @return string
     */
    public function getProductionWsUrl(): string
    {
        return $this->getValue(self::FIELD_PRODUCTION_WS_URL);
    }

    /**
     * Get sandbox ws url.
     *
     * @return string
     */
    public function getSandboxWsUrl(): string
    {
        return $this->getValue(self::FIELD_SANDBOX_WS_URL);
    }

    /**
     * Get package type.
     *
     * @return string
     */
    public function getPackageType(): string
    {
        return $this->getValue(self::FIELD_PACKAGE_TYPE);
    }

    /**
     * SSL Verify?.
     *
     * @return bool
     */
    public function sslVerify(): bool
    {
        return (bool) $this->getValue(self::FIELD_SSL_VERIFY);
    }

    /**
     * Get field value.
     *
     * @param string $field
     *
     * @return mixed
     */
    private function getValue(string $field)
    {
        $path = sprintf('carriers/%s/%s', Urbano::CODE, $field);

        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }
}

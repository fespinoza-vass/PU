<?php

namespace WolfSellers\MultipleWishlist\Logger;

use DateTimeZone;
use Monolog\DateTimeImmutable;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface as StoreScopeConfigInterface;

class Logger extends \Monolog\Logger
{
    /** @var string  */
    const XML_PATH_DEBUG_ENABLED = 'wishlist/general/debug_enabled';

    /**
     * @var StoreScopeConfigInterface
     */
    private StoreScopeConfigInterface $_scopeConfig;

    /**
     * @param StoreScopeConfigInterface $scopeConfig
     * @param string $name
     * @param array $handlers
     * @param array $processors
     * @param DateTimeZone|null $timezone
     */
    public function __construct(
        StoreScopeConfigInterface $scopeConfig,
        string $name,
        array $handlers = [],
        array $processors = [],
        ?DateTimeZone $timezone = null)
    {
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($name, $handlers, $processors, $timezone);
    }

    /**
     * @return bool
     */
    public function isLoggingActive(){
        return $this->_scopeConfig->isSetFlag(self::XML_PATH_DEBUG_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param int $level
     * @param string $message
     * @param array $context
     * @param DateTimeImmutable|null $datetime
     * @return bool
     */
    public function addRecord(int $level, string $message, array $context = [], DateTimeImmutable $datetime = null): bool {

        if(!$this->isLoggingActive()){
            return false;
        }

        return parent::addRecord($level, $message, $context, $datetime);
    }
}

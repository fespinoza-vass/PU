<?php
namespace WolfSellers\EnvioRapido\Logger;
use DateTimeZone;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 *
 */
class Logger extends \Monolog\Logger
{
    CONST XML_SAVAR_GENERATE_LOGS = "carriers/envio_rapido/logs_active";

    /** @var ScopeConfigInterface */
    protected $_scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param string $name
     * @param array $handlers
     * @param array $processors
     * @param DateTimeZone|null $timezone
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        string $name,
        array $handlers = [],
        array $processors = [],
        ?DateTimeZone $timezone = null
    ) {
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($name, $handlers, $processors, $timezone);
    }

    /**
     * @param $message
     * @param array $context
     * @return void
     */
    public function error($message, array $context = []): void
    {
        if(!$this->isLoggingActive()){
            return;
        }
        parent::error($message, $context);
    }

    /**
     * @param $message
     * @param array $context
     * @return void
     */
    public function info($message, array $context = []): void
    {
        if(!$this->isLoggingActive()){
            return;
        }

        parent::info($message, $context);
    }

    /**
     * @param $message
     * @param array $context
     * @return void
     */
    public function warning($message, array $context = []): void
    {
        if(!$this->isLoggingActive()){
            return;
        }

        parent::warning($message, $context);
    }

    /**
     * @return mixed
     */
    public function isLoggingActive(){
        return $this->_scopeConfig->getValue(self::XML_SAVAR_GENERATE_LOGS, ScopeInterface::SCOPE_STORE);
    }
}

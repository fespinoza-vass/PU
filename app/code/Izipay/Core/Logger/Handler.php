<?php
namespace Izipay\Core\Logger;

use Magento\Framework\Logger\Handler\Base as BaseHandler;
use Monolog\Logger;

class Handler extends BaseHandler
{
    protected $loggerType = Logger::DEBUG;

    protected $fileName = '/var/log/izipay.log';
}

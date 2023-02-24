<?php

namespace WolfSellers\SkinCare\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Session\SessionManagerInterface as sessionManager;

use Psr\Log\LoggerInterface as logger;

class GetSessionId extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected sessionManager $sessionManager;
    protected logger $logger;

    public function __construct(
        sessionManager $sessionManager,
        logger         $logger,
        Context        $context
    )
    {
        $this->sessionManager = $sessionManager;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * TODO Get Id for LOGIN customers
     * @return mixed|null
     */
    public function getSessionIdentificator()
    {
        $identification = null;
        $this->logger->info('----------------- Session Identificator -----------------');
        $visitorSessionData = $this->sessionManager->getData();
        if ($visitorSessionData and
            isset($visitorSessionData['visitor_data']) and
            is_array($visitorSessionData['visitor_data']) and
            isset($visitorSessionData['visitor_data']['visitor_id'])
        ) {
            $this->logger->info('Visitor Id');
            $identification = $visitorSessionData['visitor_data']['visitor_id'];
            $this->logger->info($identification);
        }

        return $identification;
    }
}

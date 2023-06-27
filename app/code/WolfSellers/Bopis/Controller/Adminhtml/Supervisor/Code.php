<?php

namespace WolfSellers\Bopis\Controller\Adminhtml\Supervisor;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Stdlib\CookieManagerInterface;

/**
 *
 */
class Code extends \Magento\Backend\App\Action
{
    private CookieManagerInterface $cookieManager;

    /**
     * @param CookieManagerInterface $cookieManager
     * @param Context $context
     */
    public function __construct(CookieManagerInterface $cookieManager, Context $context)
    {
        parent::__construct($context);
        $this->cookieManager = $cookieManager;
    }

    /**
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function execute()
    {
        $storeCode = $this->getRequest()->getParam("store_code", "all");
        $this->cookieManager->setPublicCookie("store_code", $storeCode);
    }
}

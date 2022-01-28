<?php


namespace WolfSellers\LoginUser\Observer;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Framework\Event\ObserverInterface;

class AddClassToBody implements ObserverInterface
{
    protected $pageConfig;
    protected $customerSession;

    protected $websiteScopeHelper;

    public function __construct(PageConfig $pageConfig, CustomerSession $customerSession)
    {
        $this->pageConfig = $pageConfig;
        $this->customerSession = $customerSession;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->customerSession->isLoggedIn()) {
            return;
        }

        $this->pageConfig->addBodyClass('loginuser');
    }
}

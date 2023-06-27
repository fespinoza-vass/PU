<?php

namespace WolfSellers\Bopis\Controller\Adminhtml\Listorders;

class Index extends \Magento\Backend\App\Action
{
    protected $resultPageFactory = false;
    const ADMIN_RESOURCE = 'WolfSellers_Bopis::principal';
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('WolfSellers_Bopis::principal');
        $resultPage->getConfig()->getTitle()->prepend(__('Órdenes del día'));
        return $resultPage;
    }

}

<?php

namespace WolfSellers\Bopis\Controller\Adminhtml\Listcompleteorders;

class Index extends \Magento\Backend\App\Action
{
    protected $resultPageFactory = false;
    const ADMIN_RESOURCE = 'WolfSellers_Bopis::listcompleteorders';
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
        $resultPage->setActiveMenu('WolfSellers_Bopis::listcompleteorders');
        $resultPage->getConfig()->getTitle()->prepend(__('Entregadas'));
        return $resultPage;
    }

}

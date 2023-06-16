<?php

namespace WolfSellers\Bopis\Controller\Adminhtml\Listholdedorders;

class Index extends \Magento\Backend\App\Action
{
    protected $resultPageFactory = false;
    const ADMIN_RESOURCE = 'WolfSellers_Bopis::listholdedorders';
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
        $resultPage->setActiveMenu('WolfSellers_Bopis::listholdedorders');
        $resultPage->getConfig()->getTitle()->prepend(__('Detenidas'));
        return $resultPage;
    }

}

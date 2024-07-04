<?php

namespace WolfSellers\Bopis\Controller\Adminhtml\Listshippingorders;

use \Magento\Framework\App\Config\ScopeConfigInterface;

/**
 *
 */
class Index extends \Magento\Backend\App\Action
{
    /**
     * @var bool|\Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory = false;
    /**
     *
     */
    const ADMIN_RESOURCE = 'WolfSellers_Bopis::listshippingorders';


    /**
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('WolfSellers_Bopis::listcshippingorders');
        $resultPage->getConfig()->getTitle()->prepend(__('En camino'));
        return $resultPage;
    }

}
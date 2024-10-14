<?php

namespace Izipay\Core\Controller\Adminhtml\Notification;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Izipay\Core\Model\NotificationFactory;

class Edit extends Action implements HttpGetActionInterface
{
	/**
     * Request instance
     *
     * @var \Izipay\Core\Model\Notification
     */
    protected $notificationFactory;

    /**
     * Request instance
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $rawFactory
     */
    public function __construct(
        Context $context,
        PageFactory $rawFactory,
        NotificationFactory $notificationFactory
    ) {
        $this->pageFactory = $rawFactory;
        $this->notificationFactory = $notificationFactory;

        parent::__construct($context);
    }

    /**
     * Add the main Admin Grid page
     *
     * @return Page
     */
    public function execute(): Page
    {
        $resultPage = $this->pageFactory->create();
        $resultPage->setActiveMenu('Izipay_Core::notification');
        $resultPage->getConfig()->getTitle()->prepend(__('Detalle'));
        $id  = $this->getRequest()->getParam('id');
        $data = $this->notificationFactory->create()->load($id);

        $block = $resultPage->getLayout()
            ->getBlock('izipay.notification.block.adminhtml.detail')
            ->setData('detail',$data->getData())
            ->toHtml();

        return $resultPage;
    }

    protected function _isAllowed()
	{
 		return $this->_authorization->isAllowed('Izipay_Core::izipay');
	}
}

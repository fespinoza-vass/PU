<?php


namespace WolfSellers\Bopis\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Registry;
use Magento\Framework\Translate\InlineInterface;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;
use WolfSellers\Bopis\Helper\RealStates;

/**
 *
 */
class View  extends \Magento\Sales\Controller\Adminhtml\Order
{
    /**
     * @var bool
     */
    protected $resultPageFactory = false;
    /**
     *
     */
    const ADMIN_RESOURCE = 'WolfSellers_Bopis::principal';

    /**
     * @param Action\Context $context
     * @param Registry $coreRegistry
     * @param FileFactory $fileFactory
     * @param InlineInterface $translateInline
     * @param PageFactory $resultPageFactory
     * @param JsonFactory $resultJsonFactory
     * @param LayoutFactory $resultLayoutFactory
     * @param RawFactory $resultRawFactory
     * @param OrderManagementInterface $orderManagement
     * @param OrderRepositoryInterface $orderRepository
     * @param LoggerInterface $logger
     * @param RealStates $_realStates
     */
    public function __construct(Action\Context $context, Registry $coreRegistry, FileFactory $fileFactory, InlineInterface $translateInline, PageFactory $resultPageFactory, JsonFactory $resultJsonFactory, LayoutFactory $resultLayoutFactory, RawFactory $resultRawFactory, OrderManagementInterface $orderManagement, OrderRepositoryInterface $orderRepository, LoggerInterface $logger, protected RealStates $_realStates)
    {
        parent::__construct($context, $coreRegistry, $fileFactory, $translateInline, $resultPageFactory, $resultJsonFactory, $resultLayoutFactory, $resultRawFactory, $orderManagement, $orderRepository, $logger);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $order = $this->_initOrder();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($order) {
            try {
                $resultPage = $this->_initAction();
                $menuOption = $this->_realStates->getMenuOption($order->getStatus());
                $resultPage->setActiveMenu('WolfSellers_Bopis::' . $menuOption);
                $resultPage->getConfig()->getTitle()->prepend(__('Orden'));
            } catch (\Exception $e) {
                $this->logger->critical($e);
                $this->messageManager->addErrorMessage(__('Exception occurred during order load'));
                return $this->_redirect($this->_redirect->getRefererUrl());
            }
            return $resultPage;
        }
        return $this->_redirect($this->_redirect->getRefererUrl());
    }

    /**
     * Init layout, menu and breadcrumb
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        return $this->resultPageFactory->create();
    }

}

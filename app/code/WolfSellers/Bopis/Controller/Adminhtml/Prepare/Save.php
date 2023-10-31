<?php
namespace WolfSellers\Bopis\Controller\Adminhtml\Prepare;


use Exception;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Sales\Controller\Adminhtml\Order;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Registry;
use Magento\Framework\Translate\InlineInterface;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\InputException;
use Psr\Log\LoggerInterface;
use WolfSellers\Bopis\Helper\Config;
use WolfSellers\Email\Helper\EmailHelper;

class Save extends Order
{

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'WolfSellers_Bopis::principal';

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var FileFactory
     */
    protected $_fileFactory;

    /**
     * @var InlineInterface
     */
    protected $_translateInline;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * @var RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var OrderManagementInterface
     */
    protected $orderManagement;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;


    /**
     * @var Config
     */
    protected $config;

    /**
     * @var EmailHelper
     */
    private EmailHelper $emailHelper;

    /**
     * @param Action\Context $context
     * @param Config $config
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
     * @param EmailHelper $emailHelper
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
     */
    public function __construct(
        Action\Context $context,
        Config $config,
        Registry $coreRegistry,
        FileFactory $fileFactory,
        InlineInterface $translateInline,
        PageFactory $resultPageFactory,
        JsonFactory $resultJsonFactory,
        LayoutFactory $resultLayoutFactory,
        RawFactory $resultRawFactory,
        OrderManagementInterface $orderManagement,
        OrderRepositoryInterface $orderRepository,
        LoggerInterface $logger,
        EmailHelper $emailHelper
    ) {
        $this->config = $config;
        $this->emailHelper = $emailHelper;
        parent::__construct(
            $context,
            $coreRegistry,
            $fileFactory,
            $translateInline,
            $resultPageFactory,
            $resultJsonFactory,
            $resultLayoutFactory,
            $resultRawFactory,
            $orderManagement,
            $orderRepository,
            $logger);
    }

    /**
     * Hold order
     *
     * @return Redirect|ResponseInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if (!$this->isValidPostRequest()) {
            $this->messageManager->addErrorMessage(__('No se pudo preparar la orden.'));
            $this->logger->critical("isValidPostRequest false");
            return $this->_redirect($this->_redirect->getRefererUrl());
        }
        $order = $this->_initOrder();
        if ($order) {
            try {
                $order->setStatus($this->config->getConfig('bopis/status/preparing'))
                    ->addStatusToHistory($order->getStatus())
                    ->addCommentToStatusHistory('Orden Lista para enviar');
                $this->orderRepository->save($order);

                $to = ['email' => $order->getCustomerEmail(), 'name' => $order->getCustomerName()];
                $this->emailHelper->sendPreparedOrderEmail($to, $this->emailHelper->getOrderModel($order));

                $this->logger->critical("La Orden está preparada para ser enviada");
                $this->messageManager->addSuccessMessage(__('La Orden está preparada para ser envida.'));
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__('No se pudo preparar la orden.'));
                $this->logger->critical($e->getMessage());
            }
            $resultRedirect->setPath('bopis/order/view', ['order_id' => $order->getId()]);
            return $resultRedirect;
        }
        return $this->_redirect($this->_redirect->getRefererUrl());
    }

    /**
     * Initialize order model instance
     *
     * @return \Magento\Sales\Api\Data\OrderInterface|false
     */
    protected function _initOrder()
    {
        $id = $this->getRequest()->getParam('order_id');
        try {
            $order = $this->orderRepository->get($id);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('This order no longer exists.'));
            $this->logger->critical($e->getMessage());
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return false;
        } catch (InputException $e) {
            $this->messageManager->addErrorMessage(__('This order no longer exists.'));
            $this->logger->critical($e->getMessage());
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        $this->_coreRegistry->register('sales_order', $order);
        $this->_coreRegistry->register('current_order', $order);
        return $order;
    }

}

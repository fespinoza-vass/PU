<?php

namespace WolfSellers\Bopis\Controller\Adminhtml\Shipping;

use Exception;
use Magento\Backend\App\Action\Context;
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
use WolfSellers\EnvioRapido\Helper\SavarHelper;
use WolfSellers\Bopis\Logger\Logger;

class Save extends Order
{

    /** @var SavarHelper */
    protected $_savarHelper;

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'WolfSellers_Bopis::principal';

    CONST SHIPPING_METHOD_ENVIO_RAPIDO = "envio_rapido_envio_rapido";

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
     * @var Logger
     */
    protected Logger $bopisLogger;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var EmailHelper
     */
    protected $emailHelper;

    /**
     * @param Context $context
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
     * @param Logger $bopisLogger
     * @param EmailHelper $emailHelper
     * @param SavarHelper $savarHelper
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
     */
    public function __construct(
        Action\Context           $context,
        Config                   $config,
        Registry                 $coreRegistry,
        FileFactory              $fileFactory,
        InlineInterface          $translateInline,
        PageFactory              $resultPageFactory,
        JsonFactory              $resultJsonFactory,
        LayoutFactory            $resultLayoutFactory,
        RawFactory               $resultRawFactory,
        OrderManagementInterface $orderManagement,
        OrderRepositoryInterface $orderRepository,
        LoggerInterface          $logger,
        Logger                   $bopisLogger,
        EmailHelper              $emailHelper,
        SavarHelper              $savarHelper
    )
    {
        $this->_savarHelper = $savarHelper;
        $this->config = $config;
        $this->emailHelper = $emailHelper;
        $this->bopisLogger = $bopisLogger;
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
            return $this->stopCurrentShipment('No se pudo enviar la orden.', 'isValidPostRequest false');
        }
        $order = $this->_initOrder();
        if ($order) {
            try {
                if (!$order->canShip() && $order->hasShipments()) {
                    return $this->stopCurrentShipment(
                        'La orden no se puede enviar o ya tiene un envío en proceso.',
                        'order ' . $order->getIncrementId() . ' !CanShip or hasShipments'
                    );
                }

                // Start shipping process
                $shipment = $this->_savarHelper->generateShipment($order);

                // We validate if the shipment is complete
                if (!$shipment) {
                    return $this->stopCurrentShipment(
                        'No se logró completar el envío. Asegurate que la orden tenga una sucursal asignada y
                            que esta tenga el stock suficiente para realizar el envío.',
                        'order ' . $order->getIncrementId() . ' No se logró completar, consulte savar.log'
                    );
                }

                $order->setStatus($this->config->getConfig('bopis/status/shipping'))
                    ->addStatusToHistory($order->getStatus())
                    ->addCommentToStatusHistory('Orden Lista para ser entregada');
                $this->orderRepository->save($order);

                $to = ['email' => $order->getCustomerEmail(), 'name' => $order->getCustomerName()];
                $this->emailHelper->sendShipOrderEmail($to, $this->emailHelper->getOrderModel($order));

                $this->bopisLogger->info("Shipment completed", ['order' => $order->getIncrementId()]);
                $this->messageManager->addSuccessMessage(__('La Orden está preparada para ser entregada.'));
            } catch (\Throwable $e) {
                $this->messageManager->addErrorMessage(__('No se pudo enviar la orden.'));
                $this->bopisLogger->critical($e->getMessage(), ['order' => $order->getIncrementId()]);
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
            $this->bopisLogger->critical($e->getMessage());
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return false;
        } catch (InputException $e) {
            $this->messageManager->addErrorMessage(__('This order no longer exists.'));
            $this->bopisLogger->critical($e->getMessage());
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        $this->_coreRegistry->register('sales_order', $order);
        $this->_coreRegistry->register('current_order', $order);
        return $order;
    }

    /**
     * @param $message
     * @param $reason
     * @return mixed
     */
    protected function stopCurrentShipment($message, $reason): mixed
    {
        $this->messageManager->addErrorMessage(__($message));
        $this->bopisLogger->error($reason);
        return $this->_redirect($this->_redirect->getRefererUrl());
    }

}

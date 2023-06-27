<?php

namespace WolfSellers\Bopis\Controller\Adminhtml\Confirmar;


use Exception;
use Magento\Backend\App\Action;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\Translate\InlineInterface;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Controller\Adminhtml\Order;
use Psr\Log\LoggerInterface;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Framework\DB\Transaction;

class Save extends Order
{

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'WolfSellers_Bopis::principal';

    private InvoiceService $invoiceService;
    private Transaction $transaction;

    public function __construct(
        Action\Context $context,
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
        InvoiceService $invoiceService = null,
        Transaction $transaction = null
    ){
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
            $logger
        );
        $this->invoiceService = $invoiceService ?? ObjectManager::getInstance()->create(InvoiceService::class);
        $this->transaction = $transaction ?? ObjectManager::getInstance()->create(Transaction::class);
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
                $verificacion = "verificacion_bopis_cliente_retira";
                $message = "Verificado: Cliente que Retira";

                switch ((int)$this->getRequest()->getParam("tipo_verificacion")) {
                    case 2: {
                        $verificacion = "verificacion_bopis_cliente_factura";
                        $message = "Verificado: Cliente que factura";
                        break;
                    }
                    case 3: {
                        $verificacion = "verificacion_bopis_orden";
                        $message = "Verificado: Orden";
                        break;
                    }
                    case 4: {
                        $verificacion = "verificacion_bopis_metodo_pago";
                        $message = "Verificado: Método de Pago";

                        if ($order->canInvoice()) {
                            $this->invoiceOrder($order);
                        }
                        break;
                    }
                    default: {
                        break;
                    }
                }

                $order->setData($verificacion, 1)
                    ->addCommentToStatusHistory($message);
                $this->orderRepository->save($order);
                $this->logger->critical($message);
                $this->messageManager->addSuccessMessage(__($message));
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__('No se pudo confirmar la orden.'));
                $this->logger->critical($e->getMessage());
            }
            $resultRedirect->setPath('bopis/order/view', ['order_id' => $order->getId()]);
            return $resultRedirect;
        }
        return $this->_redirect($this->_redirect->getRefererUrl());
    }

    protected function invoiceOrder($order) {
        $invoice = $this->invoiceService->prepareInvoice($order);
        $invoice->register();
        $invoice->save();

        $transactionSave =
            $this->transaction
                ->addObject($invoice)
                ->addObject($invoice->getOrder());
        $transactionSave->save();
    }

    /**
     * Initialize order model instance
     *
     * @return OrderInterface|false
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

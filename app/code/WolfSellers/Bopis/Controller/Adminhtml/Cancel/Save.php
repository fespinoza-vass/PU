<?php


namespace WolfSellers\Bopis\Controller\Adminhtml\Cancel;


use Exception;
use Magento\Backend\App\Action;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Sales\Api\Data\CreditmemoItemCreationInterface;
use Magento\Sales\Api\Data\CreditmemoItemCreationInterfaceFactory;
use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Translate\InlineInterface;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\RefundOrderInterface;
use Magento\Sales\Controller\Adminhtml\Order;
use Psr\Log\LoggerInterface;
use WolfSellers\Bopis\Model\Queue\NotificationData;
use WolfSellers\Bopis\Model\Queue\NotificationDataFactory;
use WolfSellers\Bopis\Observer\Checkout\SubmitAllAfter;

class Save extends Order
{

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'WolfSellers_BackendBopis::dashboard';
    private PublisherInterface $publisher;
    private NotificationDataFactory $notificationDataFactory;
    private Json $json;
    private RefundOrderInterface $refundOrder;
    private CreditmemoItemCreationInterfaceFactory $creditmemoItemInterfaceFactory;

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
     * @param PublisherInterface $publisher
     * @param NotificationDataFactory $notificationDataFactory
     * @param Json $json
     * @param RefundOrderInterface $refundOrder
     * @param CreditmemoItemCreationInterfaceFactory $creditmemoItemInterfaceFactory
     */
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
        PublisherInterface $publisher,
        NotificationDataFactory $notificationDataFactory,
        Json $json,
        RefundOrderInterface $refundOrder,
        CreditmemoItemCreationInterfaceFactory $creditmemoItemInterfaceFactory
    ) {
        parent::__construct($context, $coreRegistry, $fileFactory, $translateInline, $resultPageFactory, $resultJsonFactory, $resultLayoutFactory, $resultRawFactory, $orderManagement, $orderRepository, $logger);
        $this->publisher = $publisher;
        $this->notificationDataFactory = $notificationDataFactory;
        $this->json = $json;
        $this->refundOrder = $refundOrder;
        $this->creditmemoItemInterfaceFactory = $creditmemoItemInterfaceFactory;
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
            $this->messageManager->addErrorMessage(__('You have not put the order on hold.'));
            return $this->_redirect($this->_redirect->getRefererUrl());
        }
        $order = $this->_initOrder();
        if ($order) {
            try {
                $this->refundItems($order->getItems(), $order->getId());
                $this->messageManager->addSuccessMessage(__('La Orden fue cancelada.'));
                $this->publishNotification($order);
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__('You have not put the order on hold.' . $e->getMessage()));
            }
            $resultRedirect->setPath('bopis/order/view', ['order_id' => $order->getId()]);
            return $resultRedirect;
        }
        return $this->_redirect($this->_redirect->getRefererUrl());
    }

    private function publishNotification(OrderInterface $order)
    {

        /** @var NotificationData $notificationData */
        $notificationData = $this->notificationDataFactory->create();
        $notificationData->setOrderId($order->getId());
        $notificationData->setType(NotificationData::CANCELED_ORDER);
        $this->publisher->publish(SubmitAllAfter::TOPIC_NAME, $notificationData);
    }

    /**
     * @param OrderItemInterface[] $items
     * @param $orderId
     * @return void
     */
    private function refundItems(array $items, $orderId) {

        if(sizeof($items) < 0) {
            return;
        }
        $invoiceItems = [];

        foreach ($items as $item) {
            /** @var CreditmemoItemCreationInterface $creditmemoItemCreation */
            $creditmemoItemCreation = $this->creditmemoItemInterfaceFactory->create();
            $invoiceItems[] = $creditmemoItemCreation->setQty($item->getQtyOrdered())
                ->setOrderItemId($item->getItemId());
        }

        /**
         * Create Credit Memo
         */
        $this->refundOrder->execute($orderId, $invoiceItems, true);
    }

}

<?php

namespace WolfSellers\Bopis\Controller\Adminhtml\Complete;


use Exception;
use Magento\Backend\App\Action;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\Framework\Registry;
use Magento\Framework\Translate\InlineInterface;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Controller\Adminhtml\Order;
use Psr\Log\LoggerInterface;
use WolfSellers\Bopis\Model\Queue\NotificationData;
use WolfSellers\Bopis\Model\Queue\NotificationDataFactory;
use WolfSellers\Bopis\Observer\Checkout\SubmitAllAfter;
use \Magento\Sales\Model\OrderFactory;

class Save extends Order
{

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'WolfSellers_Bopis::principal';
    private PublisherInterface $publisher;
    private NotificationDataFactory $notificationDataFactory;
    protected OrderFactory $orderFactory;

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
        OrderFactory $orderFactory
    ) {
        parent::__construct($context, $coreRegistry, $fileFactory, $translateInline, $resultPageFactory, $resultJsonFactory, $resultLayoutFactory, $resultRawFactory, $orderManagement, $orderRepository, $logger);
        $this->publisher = $publisher;
        $this->notificationDataFactory = $notificationDataFactory;
        $this->orderFactory = $orderFactory;
    }

    /**
     * Hold order
     *
     * @return Redirect|ResponseInterface
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam("order_id");
        $comment = $this->getRequest()->getParam("complete_comments");
        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$comment || !$orderId):
            $this->messageManager->addErrorMessage(__('Comentario vació favor de completar el formulario.'));
            return $this->_redirect($this->_redirect->getRefererUrl());
        endif;
        $order = $this->orderFactory->create()->load($orderId);
        if ($order):
            $history = $order->addStatusHistoryComment($comment);
            $history->save();
            $order->save();
            return $this->_redirect($this->_redirect->getRefererUrl());
        else:
            $this->messageManager->addErrorMessage(__('No se pudo preparar la orden.'));
        endif;

        return $this->_redirect($this->_redirect->getRefererUrl());
    }

    private function publishNotification(OrderInterface $order)
    {
        /** @var NotificationData $notificationData */
        $notificationData = $this->notificationDataFactory->create();
        $notificationData->setType(NotificationData::HOLDED_ORDER);
        $notificationData->setOrderId($order->getId());
        $this->publisher->publish(SubmitAllAfter::TOPIC_NAME, $notificationData);
    }

}

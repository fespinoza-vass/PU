<?php


namespace WolfSellers\Bopis\Controller\Adminhtml\Hold;


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
use Magento\Framework\Serialize\Serializer\Json;
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

/**
 *
 */
class Save extends Order
{

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'WolfSellers_Bopis::principal';
    /**
     * @var PublisherInterface
     */
    private PublisherInterface $publisher;
    /**
     * @var NotificationDataFactory
     */
    private NotificationDataFactory $notificationDataFactory;
    /**
     * @var Json
     */
    private Json $json;

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
        Json $json
    ) {
        parent::__construct($context, $coreRegistry, $fileFactory, $translateInline, $resultPageFactory, $resultJsonFactory, $resultLayoutFactory, $resultRawFactory, $orderManagement, $orderRepository, $logger);
        $this->publisher = $publisher;
        $this->notificationDataFactory = $notificationDataFactory;
        $this->json = $json;
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
                $this->orderManagement->hold($order->getEntityId());
                $bopisReason = $this->getRequest()->getParam("bopis_hold_reason");
                $bopisComment = $this->getRequest()->getParam("bopis_hold_comment");
                $order->setData("bopis_hold_reason", $bopisReason)
                    ->setData("bopis_hold_comments", $bopisComment)
                    ->addStatusToHistory($order->getStatus())
                    ->addCommentToStatusHistory('La Orden se detuvo. Razón: $bopisReason');
                $order->addCommentToStatusHistory($bopisComment);
                $this->orderRepository->save($order);
                $this->messageManager->addSuccessMessage(__('La Orden se detuvo.'));
                $this->publishNotification($order);
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__('You have not put the order on hold.' . $e->getMessage()));
            }
            $resultRedirect->setPath('bopis/order/view', ['order_id' => $order->getId()]);
            return $resultRedirect;
        }
        return $this->_redirect($this->_redirect->getRefererUrl());
    }

    /**
     * @param OrderInterface $order
     * @return void
     */
    private function publishNotification(OrderInterface $order)
    {

        /** @var NotificationData $notificationData */
        $notificationData = $this->notificationDataFactory->create();
        $notificationData->setType(NotificationData::HOLDED_ORDER);
        $notificationData->setOrderId($order->getId());
        $this->publisher->publish(SubmitAllAfter::TOPIC_NAME, $notificationData);
    }

}

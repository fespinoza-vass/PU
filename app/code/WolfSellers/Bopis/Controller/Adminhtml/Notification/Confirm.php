<?php

namespace WolfSellers\Bopis\Controller\Adminhtml\Notification;


use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\Model\Auth\Session as AuthSession;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
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
use WolfSellers\Bopis\Api\BopisRepositoryInterface;
use WolfSellers\Bopis\Api\Data\NotificationInterface;
use WolfSellers\Bopis\Model\NotificationRepository;

class Confirm extends Order
{

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'WolfSellers_BackendBopis::dashboard';

    private InvoiceService $invoiceService;
    private Transaction $transaction;
    private AuthSession $authSession;
    private NotificationRepository $notificationRepository;
    private BopisRepositoryInterface $bopisRepository;
    private UrlInterface $urlBuilder;

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
     * @param AuthSession $authSession
     * @param NotificationRepository $notificationRepository
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
        AuthSession $authSession,
        NotificationRepository $notificationRepository,
        BopisRepositoryInterface $bopisRepository,
        UrlInterface $urlBuilder
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
        $this->authSession = $authSession;
        $this->notificationRepository = $notificationRepository;
        $this->bopisRepository = $bopisRepository;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Hold order
     *
     * @return Json
     */
    public function execute()
    {
        $result = "OK";
        $resultJson = $this->resultJsonFactory->create();
        $notificationId = $this->getRequest()->getParam('notification_id');
        try {
            $notification = $this->notificationRepository->get($notificationId);
            $notification->setRead(1);

            $this->notificationRepository->save($notification);
        } catch (\Exception $e) {
            $result = "Error";
        }
        return $resultJson->setData(["result" => $result ]);
    }

}

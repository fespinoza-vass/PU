<?php


namespace WolfSellers\Bopis\Model\Queue;


use Exception;
use Magento\Framework\App\Area;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Sales\Model\Order\Email\Container\IdentityInterface;
use Magento\Sales\Model\Order\Email\Container\Template;
use Magento\Sales\Model\Order\Email\Sender;
use Magento\User\Model\ResourceModel\User\Collection as AdminUserCollection;
use WolfSellers\Bopis\Model\Order\Email\SenderBuilder;
use WolfSellers\Bopis\Model\Order\Email\SenderBuilderFactory;
use Magento\Sales\Model\OrderFactory;
use Magento\User\Model\ResourceModel\User\CollectionFactory as AdminUserCollectionFactory;
use Magento\User\Model\User;
use Psr\Log\LoggerInterface;
use WolfSellers\Bopis\Api\BopisRepositoryInterface;
use WolfSellers\Bopis\Api\Data\NotificationInterface;
use WolfSellers\Bopis\Api\Data\NotificationInterfaceFactory;
use WolfSellers\Bopis\Model\Email\Identity\CancelOrder;
use WolfSellers\Bopis\Model\Email\Identity\HoldOrder;
use WolfSellers\Bopis\Model\Email\Identity\NewOrder;
use WolfSellers\Bopis\Model\NotificationRepository;

class SendNotification
{
    /**
     * @var SenderBuilderFactory
     */
    protected $senderBuilderFactory;

    /**
     * @var Template
     */
    protected $templateContainer;

    /**
     * @var IdentityInterface
     */
    protected $identityContainer;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Renderer
     */
    protected $addressRenderer;
    private NewOrder $newOrderIdentity;
    private CancelOrder $cancelOrderIdentity;
    private HoldOrder $holdOrderIdentity;
    private OrderFactory $orderFactory;
    private NotificationInterfaceFactory $notificationInterfaceFactory;
    private AdminUserCollectionFactory $adminUserCollectionFactory;
    private BopisRepositoryInterface $bopisRepository;
    private NotificationRepository $notificationRepository;

    /**
     * @param Template $templateContainer
     * @param IdentityInterface $identityContainer
     * @param SenderBuilderFactory $senderBuilderFactory
     * @param LoggerInterface $logger
     * @param Renderer $addressRenderer
     */
    public function __construct(
        Template $templateContainer,
        SenderBuilderFactory $senderBuilderFactory,
        LoggerInterface $logger,
        Renderer $addressRenderer,
        NewOrder $newOrderIdentity,
        CancelOrder $cancelOrderIdentity,
        HoldOrder $holdOrderIdentity,
        OrderFactory $orderFactory,
        NotificationInterfaceFactory $notificationInterfaceFactory,
        AdminUserCollectionFactory $adminUserCollectionFactory,
        BopisRepositoryInterface $bopisRepository,
        NotificationRepository $notificationRepository
    ) {
        $this->templateContainer = $templateContainer;
        $this->senderBuilderFactory = $senderBuilderFactory;
        $this->logger = $logger;
        $this->addressRenderer = $addressRenderer;
        $this->newOrderIdentity = $newOrderIdentity;
        $this->cancelOrderIdentity = $cancelOrderIdentity;
        $this->holdOrderIdentity = $holdOrderIdentity;
        $this->orderFactory = $orderFactory;
        $this->notificationInterfaceFactory = $notificationInterfaceFactory;
        $this->adminUserCollectionFactory = $adminUserCollectionFactory;
        $this->bopisRepository = $bopisRepository;
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * @param NotificationData $data
     */
    public function execute(NotificationData $data) {

        switch ($data->getType()) {
            case NotificationData::CANCELED_ORDER: {
                $this->identityContainer = $this->cancelOrderIdentity;
                break;
            }
            case NotificationData::HOLDED_ORDER: {
                $this->identityContainer = $this->holdOrderIdentity;
                break;
            }
            case NotificationData::NEW_ORDER:
            default: {
                $this->identityContainer = $this->newOrderIdentity;
                break;
            }
        }
        $order = $this->orderFactory->create()->load($data->getOrderId());


        $bopis = $this->bopisRepository->getByQuoteId($order->getQuoteId());

        // USUARIOS TIENDAS

        $adminUserCollection = $this->adminUserCollectionFactory->create();
        $adminUserCollection->addFilter("source_code", $bopis->getStore());

        $this->sendNotifications($adminUserCollection, $order, $data);

        // USUARIOS SUPERVISORES

        $adminUserCollection = $this->adminUserCollectionFactory->create();
        $adminUserCollection->addFilter("user_type", 2);
        $adminUserCollection->addFilter("website_id", $order->getStore()->getWebsiteId());

        $this->sendNotifications($adminUserCollection, $order, $data);
    }

    /**
     * @param AdminUserCollection $adminUserCollection
     * @param Order $order
     * @param NotificationData $data
     * @throws LocalizedException
     */
    protected function sendNotifications(
        AdminUserCollection $adminUserCollection,
        Order $order,
        NotificationData $data
    ) {

        /** @var User $user */
        foreach ($adminUserCollection->getItems() as $user) {
            /** @var NotificationInterface $notificationInterface */
            $notificationInterface = $this->notificationInterfaceFactory->create();
            $notificationInterface->setOrderId($order->getId());
            $notificationInterface->setUserId($user->getId());
            $notificationInterface->setType($data->getType());

            $this->notificationRepository->save($notificationInterface);
        }

        $this->checkAndSend($order, $adminUserCollection->getItems());

    }

    /**
     * Send order email if it is enabled in configuration.
     *
     * @param Order $order
     * @param User[]|DataObject[] $users
     */
    protected function checkAndSend(Order $order, array $users)
    {
        $this->identityContainer->setStore($order->getStore());
        $this->prepareTemplate($order);

        /** @var SenderBuilder $sender */
        $sender = $this->getSender();

        try {
            $sender->sendCopyTo();
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }
        /** @var User $user */
        foreach ($users as $user) {
            try {
                $sender->sendNotificationEmail($user->getEmail(), $user->getFirstName() . " " . $user->getLastName());
            } catch (Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }

    /**
     * Populate order email template with customer information.
     *
     * @param Order $order
     * @return void
     */
    protected function prepareTemplate(Order $order)
    {
        $transport = [
            'order' => $order,
            'order_id' => $order->getId(),
            'billing' => $order->getBillingAddress(),
            'store' => $order->getStore(),
            'created_at_formatted' => $order->getCreatedAtFormatted(2),
            'order_data' => [
                'customer_name' => $order->getCustomerName(),
                'is_not_virtual' => $order->getIsNotVirtual(),
                'email_customer_note' => $order->getEmailCustomerNote(),
                'frontend_status_label' => $order->getFrontendStatusLabel()
            ]
        ];

        $transportObject = new DataObject($transport);
        $this->templateContainer->setTemplateVars($transportObject->getData());
        $this->templateContainer->setTemplateOptions($this->getTemplateOptions($order));

        if ($order->getCustomerIsGuest()) {
            $templateId = $this->identityContainer->getGuestTemplateId();
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $templateId = $this->identityContainer->getTemplateId();
            $customerName = $order->getCustomerName();
        }

        $this->identityContainer->setCustomerName($customerName);
        $this->identityContainer->setCustomerEmail($order->getCustomerEmail());
        $this->templateContainer->setTemplateId($templateId);
    }

    /**
     * Create Sender object using appropriate template and identity.
     *
     * @return Sender
     */
    protected function getSender()
    {
        return $this->senderBuilderFactory->create(
            [
                'templateContainer' => $this->templateContainer,
                'identityContainer' => $this->identityContainer,
            ]
        );
    }

    /**
     * Get template options.
     *
     * @return array
     */
    protected function getTemplateOptions(OrderInterface $order)
    {
        return [
            'area' => Area::AREA_FRONTEND,
            'store' => $order->getStore()->getStoreId()
        ];
    }

}

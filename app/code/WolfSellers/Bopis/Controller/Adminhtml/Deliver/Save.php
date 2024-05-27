<?php

namespace WolfSellers\Bopis\Controller\Adminhtml\Deliver;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Auth\Session as AuthSession;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\Translate\InlineInterface;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Inventory\Model\ResourceModel\SourceItem\Collection;
use Magento\Inventory\Model\ResourceModel\SourceItem\CollectionFactory;
use Magento\Inventory\Model\SourceItem;
use Magento\Inventory\Model\SourceItemFactory;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Controller\Adminhtml\Order;
use Magento\Sales\Model\Convert\Order as ConvertOrder;
use Magento\Sales\Model\Order as ModelOrder;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\Order\ShipmentRepository;
use Magento\Shipping\Model\ShipmentNotifier;
use Psr\Log\LoggerInterface;
use Magento\Inventory\Model\ResourceModel\SourceItem\SaveMultiple;
use WolfSellers\Email\Helper\EmailHelper;
use Magento\Sales\Model\OrderFactory;
use WolfSellers\EnvioRapido\Helper\SavarHelper;
use WolfSellers\Bopis\Logger\Logger as BopisLogger;
use WolfSellers\Bopis\Model\ResourceModel\AbstractBopisCollection;

class Save extends Order
{

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'WolfSellers_Bopis::principal';
    private ConvertOrder $convertOrder;
    private AuthSession $authSession;
    private ShipmentRepository $shipmentRepository;
    private ShipmentNotifier $shipmentNotifier;
    private SourceItemFactory $sourceItemFactory;
    private CollectionFactory $sourceItemCollectionFactory;

    /**
     * @var SaveMultiple
     */
    private SaveMultiple $saveMultiple;
    /** @var EmailHelper */
    private EmailHelper $emailHelper;
    /** @var OrderFactory */
    private OrderFactory $orderFactory;

    /**
     * @var SavarHelper
     */
    private SavarHelper $_savarHelper;

    /**
     * @var BopisLogger
     */
    private BopisLogger $bopisLogger;

    /**
     * @param ShipmentRepository $shipmentRepository
     * @param ConvertOrder $convertOrder
     * @param ShipmentNotifier $shipmentNotifier
     * @param AuthSession $authSession
     * @param Context $context
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
     * @param SourceItemFactory $sourceItemFactory
     * @param CollectionFactory $sourceItemCollectionFactory
     * @param SaveMultiple $saveMultiple
     * @param EmailHelper $emailHelper
     * @param OrderFactory $orderFactory
     * @param BopisLogger $bopisLogger
     * @param SavarHelper $savarHelper
     */
    public function __construct(
        ShipmentRepository       $shipmentRepository,
        ConvertOrder             $convertOrder,
        ShipmentNotifier         $shipmentNotifier,
        AuthSession              $authSession,
        Action\Context           $context,
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
        SourceItemFactory        $sourceItemFactory,
        CollectionFactory        $sourceItemCollectionFactory,
        SaveMultiple             $saveMultiple,
        EmailHelper              $emailHelper,
        OrderFactory             $orderFactory,
        BopisLogger              $bopisLogger,
        SavarHelper              $savarHelper
    )
    {
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
        $this->convertOrder = $convertOrder;
        $this->authSession = $authSession;
        $this->shipmentRepository = $shipmentRepository;
        $this->shipmentNotifier = $shipmentNotifier;
        $this->sourceItemFactory = $sourceItemFactory;
        $this->sourceItemCollectionFactory = $sourceItemCollectionFactory;
        $this->saveMultiple = $saveMultiple;
        $this->emailHelper = $emailHelper;
        $this->orderFactory = $orderFactory;
        $this->_savarHelper = $savarHelper;
        $this->bopisLogger = $bopisLogger;
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
            $this->messageManager->addErrorMessage(__('No se pudo completar la orden.'));
            return $this->_redirect($this->_redirect->getRefererUrl());
        }
        $order = $this->_initOrder();
        if ($order) {
            try {
                if ($order->getShippingMethod() === AbstractBopisCollection::PICKUP_SHIPPING_METHOD) {
                    $this->bopisLogger->info('Se creara el shipment para una orden tipo pickup', ['order' => $order->getIncrementId()]);

                    $shipment = $this->_savarHelper->generateShipment($order);

                    // We validate if the shipment is complete
                    if (!$shipment) {
                        $this->messageManager->addErrorMessage(__('No se logró completar el envío. Asegurate que la orden tenga una sucursal asignada y
                            que esta tenga el stock suficiente para realizar el envío.'));
                        $this->bopisLogger->error('No se creo el shipment para pickup', ['order' => $order->getIncrementId()]);
                        return $this->_redirect($this->_redirect->getRefererUrl());
                    }
                }

                $bopisComment = $this->getRequest()->getParam("bopis_deliver_comment", "Sin comentarios");

                $order->setStatus(ModelOrder::STATE_COMPLETE)
                    ->setState(ModelOrder::STATE_COMPLETE)
                    ->addStatusToHistory($order->getStatus())
                    ->addCommentToStatusHistory('Orden Entregada');
                $order->addCommentToStatusHistory("Comentario de entrega: <br />" . $bopisComment);
                $this->orderRepository->save($order);

                $to = ['email' => $order->getCustomerEmail(), 'name' => $order->getCustomerName()];
                $this->emailHelper->sendSatisfactionSurveyEmail($to, $this->getOrderModel($order));

                $this->messageManager->addSuccessMessage(__('La Orden ha sido entregada.'));
            } catch (Exception $e) {
                $this->bopisLogger->error($e->getMessage());
                $this->bopisLogger->error($e->getTraceAsString());
                $this->messageManager->addErrorMessage(__('No se pudo entregar la orden.'));
            }
            $resultRedirect->setPath('bopis/order/view', ['order_id' => $order->getId()]);
            return $resultRedirect;
        }
        return $this->_redirect($this->_redirect->getRefererUrl());
    }

    /**
     * @param OrderInterface $order
     * @param $comment
     * @return Shipment
     * @throws LocalizedException
     */
    private function prepareShipment(OrderInterface $order, $comment): Shipment
    {
        $shipment = $this->convertOrder->toShipment($order);
        $sourceCode = $this->authSession->getUser()->getData('source_code');
        foreach ($order->getAllItems() as $orderItem) {
            // Check if order item has qty to ship or is virtual
            if ($orderItem->getIsVirtual()) {
                continue;
            }

            $qtyShipped = $orderItem->getQtyOrdered();
            // Create shipment item with qty
            $shipmentItem
                = $this->convertOrder->itemToShipmentItem($orderItem)
                ->setQty($qtyShipped);
            // Add shipment item to shipment
            $shipment->addItem($shipmentItem);

            try {

                /** @var SourceItem $sourceItem */
                /** @var Collection $sourceItemCollection */
                $sourceItemCollection = $this->sourceItemCollectionFactory->create();

                $sourceItemCollection->addFieldToFilter("source_code", $sourceCode);
                $sourceItemCollection->addFieldToFilter("sku", $orderItem->getSku());
                if ($sourceItemCollection->getSize() < 1) {
                    throw new Exception("no items");
                }
                $sourceItem = $sourceItemCollection->getFirstItem();
                $sourceItem->setQuantity($sourceItem->getQuantity() + $qtyShipped);

            } catch (Exception $e) {
                $sourceItem = $this->sourceItemFactory->create();
                $sourceItem->setQuantity($qtyShipped);
            }
            $sourceItem->setStatus(SourceItemInterface::STATUS_IN_STOCK);
            $sourceItem->setSku($orderItem->getSku());
            //$this->saveMultiple->execute([$sourceItem]);
        }
        $shipment->register();
        //$shipment->getOrder()->setIsInProcess(true);
        $shipment->addComment($comment, false, true);

        $shipment->getExtensionAttributes()->setSourceCode($sourceCode);
        return $shipment;
    }

    /**
     * @param $order
     * @return ModelOrder
     */
    public function getOrderModel($order)
    {
        return $this->orderFactory->create()->loadByIncrementId($order->getIncrementId());
    }
}

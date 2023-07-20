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
use Zend\Log\Logger;
use Magento\Inventory\Model\ResourceModel\SourceItem\SaveMultiple;


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
     * @var Logger
     */
    protected $_logger;

    /**
     * @var SaveMultiple
     */
    private SaveMultiple $saveMultiple;

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
     */
    public function __construct(
        ShipmentRepository $shipmentRepository,
        ConvertOrder $convertOrder,
        ShipmentNotifier $shipmentNotifier,
        AuthSession $authSession,
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
        SourceItemFactory $sourceItemFactory,
        CollectionFactory $sourceItemCollectionFactory,
        SaveMultiple $saveMultiple
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
        $this->convertOrder = $convertOrder;
        $this->authSession = $authSession;
        $this->shipmentRepository = $shipmentRepository;
        $this->shipmentNotifier = $shipmentNotifier;
        $this->sourceItemFactory = $sourceItemFactory;
        $this->sourceItemCollectionFactory = $sourceItemCollectionFactory;
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/bopis.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $this->_logger = $logger;
        $this->saveMultiple = $saveMultiple;
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
            return $this->_redirect($this->_redirect->getRefererUrl());
        }
        $order = $this->_initOrder();
        if ($order) {
            try {
                $bopisComment = $this->getRequest()->getParam("bopis_deliver_comment", "Sin comentarios");
               // $shipment = $this->prepareShipment($order, $bopisComment);
               // $this->shipmentRepository->save($shipment);
                //$this->shipmentNotifier->notify($shipment);
                $order//->setData("bopis_delivered", 1)
                    //->setData("bopis_deliver_comments", $bopisComment)
                    ->setStatus(ModelOrder::STATE_COMPLETE)
                    ->setState(ModelOrder::STATE_COMPLETE)
                    ->addStatusToHistory($order->getStatus())
                    ->addCommentToStatusHistory('Orden Entregada');
                $order->addCommentToStatusHistory("Comentario de entrega: <br />" . $bopisComment);
                $this->orderRepository->save($order);
                $this->messageManager->addSuccessMessage(__('La Orden ha sido entregada.'));
            } catch (Exception $e) {
                $this->_logger->err(var_export($e->getMessage()));
                $this->_logger->err(var_export($e->getTraceAsString()));
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
                if($sourceItemCollection->getSize() < 1) {
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

}

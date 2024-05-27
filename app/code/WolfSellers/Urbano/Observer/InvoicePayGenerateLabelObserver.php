<?php
/**
 * Created by.
 *
 * User: Juan Carlos Hdez <juanhernandez@wolfsellers.com>
 * Date: 2022-07-08
 * Time: 17:01
 */

declare(strict_types=1);

namespace WolfSellers\Urbano\Observer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DB\TransactionFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\ShipmentSender;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\Order\Shipment\Item;
use Magento\Sales\Model\Order\Shipment\ShipmentValidatorInterface;
use Magento\Sales\Model\Order\Shipment\Validation\QuantityValidator;
use Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader;
use Magento\Shipping\Model\Shipping\LabelGenerator;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;
use WolfSellers\Urbano\Model\Carrier\Urbano;
use WolfSellers\Urbano\Model\Source\Packaging;

/**
 * Urbano Invoice Pay Generate Label Observer.
 */
class InvoicePayGenerateLabelObserver implements ObserverInterface
{
    const ORDER_FREE_SHIPPING_METHOD = 'freeshipping';
    /** @var ShipmentLoader */
    private ShipmentLoader $shipmentLoader;

    /** @var Order  */
    private Order $order;

    /** @var Shipment */
    private Shipment $shipment;

    /** @var Http */
    private RequestInterface $request;

    /** @var ShipmentValidatorInterface */
    private ShipmentValidatorInterface $shipmentValidator;

    /** @var LabelGenerator */
    private LabelGenerator $labelGenerator;

    /** @var TransactionFactory */
    private TransactionFactory $transactionFactory;

    /** @var ScopeConfigInterface */
    private ScopeConfigInterface $scopeConfig;

    /** @var ShipmentSender */
    private ShipmentSender $shipmentSender;

    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /**
     * @param ShipmentLoader $shipmentLoader
     * @param RequestInterface $request
     * @param ShipmentValidatorInterface $shipmentValidator
     * @param LabelGenerator $labelGenerator
     * @param TransactionFactory $transactionFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param ShipmentSender $shipmentSender
     * @param LoggerInterface $logger
     */
    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Model\Convert\Order $convertOrder,
        \Magento\Shipping\Model\ShipmentNotifier $shipmentNotifier,
        ShipmentLoader $shipmentLoader,
        RequestInterface $request,
        ShipmentValidatorInterface $shipmentValidator,
        LabelGenerator $labelGenerator,
        TransactionFactory $transactionFactory,
        ScopeConfigInterface $scopeConfig,
        ShipmentSender $shipmentSender,
        LoggerInterface $logger
    ) {
        $this->_orderRepository = $orderRepository;
        $this->_convertOrder = $convertOrder;
        $this->_shipmentNotifier = $shipmentNotifier;
        $this->shipmentLoader = $shipmentLoader;
        $this->request = $request;
        $this->shipmentValidator = $shipmentValidator;
        $this->labelGenerator = $labelGenerator;
        $this->transactionFactory = $transactionFactory;
        $this->scopeConfig = $scopeConfig;
        $this->shipmentSender = $shipmentSender;
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(Observer $observer)
    {
        $this->order = $observer->getOrder();

        if (Urbano::CODE == $this->order->getShippingMethod(true)->getCarrierCode()
            && $this->autoGenerateShipment()
        ) {
            try {
                $this->generateShipment();
            } catch (\Exception $e) {
                $this->logger->error('Error generate shipment: '.$e->getMessage());
            }
        }

        if (self::ORDER_FREE_SHIPPING_METHOD == $this->order->getShippingMethod(true)->getCarrierCode()
            && $this->autoGenerateShipment()
        ) {
            try {
                $this->generateShipmentFree();
            } catch (\Exception $e) {
                $this->logger->error('Error generate free shipment: '.$e->getMessage());
            }
        }
        return;
    }

    private function generateShipmentFree(){
        $order = $this->order;
        // to check order can ship or not
        if (!$order->canShip()) {
            $this->logger->error('You cant create the free Shipment of this order.');
        }
        $orderShipment = $this->_convertOrder->toShipment($order);
        foreach ($order->getAllItems() AS $orderItem) {
         // Check virtual item and item Quantity
         if (!$orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
            continue;
         }
         $qty = $orderItem->getQtyToShip();
         $shipmentItem = $this->_convertOrder->itemToShipmentItem($orderItem)->setQty($qty);
         $orderShipment->addItem($shipmentItem);
        }
        $orderShipment->register();
        $orderShipment->getOrder()->setIsInProcess(true);
        try {
            // Save created Order Shipment
            $orderShipment->save();
            $orderShipment->getOrder()->save();
            // Send Shipment Email
            $this->_shipmentNotifier->notify($orderShipment);
            $orderShipment->save();
        } catch (\Exception $e) {
            $this->logger->error('Error generate free shipment: '.$e->getMessage());
        }
    }

    /**
     * Generate shipment.
     *
     * @return void
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Sales\Exception\DocumentValidationException
     */
    private function generateShipment()
    {
        $this->shipmentLoader->setOrderId($this->order->getId());
        $shipment = $this->shipmentLoader->load();

        if (!$shipment) {
            return;
        }

        $this->shipment = $shipment;

        $shipment->addComment(__('Auto generated by invoice pay'));

        $validationResult = $this->shipmentValidator->validate($shipment, [QuantityValidator::class]);
        if ($validationResult->hasMessages()) {
            $messages = implode(' ', $validationResult->getMessages());
            $this->logger->error('Shipment Document Validation Error(s): '.$messages);

            return;
        }

        $this->request->setParam('packages', $this->generatePackages());

        $shipment->register();
        $shipment->getOrder()->setCustomerNoteNotify(true);
        $this->labelGenerator->create($shipment, $this->request);
        $this->saveShipment($shipment);
        $this->shipmentSender->send($shipment);
    }

    /**
     * Save shipment and order in one transaction.
     *
     * @param Shipment $shipment
     *
     * @return $this
     */
    protected function saveShipment(Shipment $shipment)
    {
        $transaction = $this->transactionFactory->create();
        $shipment->getOrder()->setIsInProcess(true);
        $transaction->addObject(
            $shipment
        )->addObject(
            $shipment->getOrder()
        )->save();

        return $this;
    }

    /**
     * Generate custom packages.
     *
     * @return array
     */
    private function generatePackages(): array
    {
        $items = [];
        $weightTotal = 0;
        $amountTotal = 0;

        /** @var Item $shipmentItem */
        foreach ($this->shipment->getAllItems() as $shipmentItem) {
            $weightTotal += $shipmentItem->getQty() * $shipmentItem->getWeight();
            $amountTotal += $shipmentItem->getQty() * $shipmentItem->getPrice();

            $items[$shipmentItem->getOrderItemId()] = [
                'qty' => $shipmentItem->getQty(),
                'customs_value' => $shipmentItem->getPrice(),
                'price' => $shipmentItem->getPrice(),
                'name' => $shipmentItem->getName(),
                'weight' => (float) $shipmentItem->getWeight(),
                'product_id' => $shipmentItem->getProductId(),
                'order_item_id' => $shipmentItem->getOrderItemId(),
            ];
        }

        $packageWeight = (int) $this->getConfigData('package_weight');

        if (!$packageWeight) {
            $packageWeight = $weightTotal;
        }

        $params = [
            'container' => Packaging::PACKAGE,
            'weight' => $packageWeight,
            'customs_value' => $amountTotal,
            'length' => (int) $this->getConfigData('package_length'),
            'width' => (int) $this->getConfigData('package_width'),
            'height' => (int) $this->getConfigData('package_height'),
            'weight_units' => \Zend_Measure_Weight::KILOGRAM,
            'dimension_units' => \Zend_Measure_Length::CENTIMETER,
        ];

        $packages = [];
        $packages[] = [
            'params' => $params,
            'items' => $items,
        ];

        return $packages;
    }

    /**
     * Retrieve information from carrier configuration.
     *
     * @param string $field
     *
     * @return false|string
     */
    private function getConfigData(string $field)
    {
        $path = sprintf('carriers/%s/%s', Urbano::CODE, $field);

        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $this->order->getStoreId()
        );
    }

    /**
     * Auto generate shipment.
     *
     * @return bool
     */
    private function autoGenerateShipment(): bool
    {
        return (bool) $this->getConfigData('auto_generate_shipment');
    }
}

<?php

namespace WolfSellers\OrderQR\Block\Order\Email;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;


class QR extends \Magento\Framework\View\Element\Template
{

    // shipping methods allowed to generate QR
    CONST SHIPPING_METHOD_FOR_QRCODE = "instore_pickup";

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param Context $context
     * @param array $data
     * @param OrderRepositoryInterface|null $orderRepository
     */
    public function __construct(
        Context $context,
        ?OrderRepositoryInterface $orderRepository = null,
        array $data = [],
    ) {
        $this->orderRepository = $orderRepository ?: ObjectManager::getInstance()->get(OrderRepositoryInterface::class);

        parent::__construct($context, $data);
    }

    public function getOrder()
    {
        $order = $this->getData('order');

        if ($order !== null) {
            return $order;
        }
        $orderId = (int)$this->getData('order_id');
        if ($orderId) {
            $order = $this->orderRepository->get($orderId);
            $this->setData('order', $order);
        }

        return $this->getData('order');
    }


    protected function _beforeToHtml()
    {
        $this->prepareBlockData();
        return parent::_beforeToHtml();
    }
    /**
     * Prepares block data
     *
     * @return void
     */
    protected function prepareBlockData()
    {
        $order = $this->getOrder();

        $this->addData(
            [
                'is_order_visible' => $this->isVisible($order),
                'view_order_url' => $this->getUrl(
                    'sales/order/view/',
                    ['order_id' => $order->getEntityId()]
                ),
                'print_url' => $this->getUrl(
                    'sales/order/print',
                    ['order_id' => $order->getEntityId()]
                ),
                'can_print_order' => $this->isVisible($order),
                'can_view_order'  => $this->canViewOrder($order),
                'order_id'  => $order->getIncrementId(),
                'qr_image'  => $this->_qrHelper->getURLQRImage($order->getIncrementId()),
                'is_pickup'  => boolval(($order->getShippingMethod() == self::SHIPPING_METHOD_FOR_QRCODE))
            ]
        );
    }
}

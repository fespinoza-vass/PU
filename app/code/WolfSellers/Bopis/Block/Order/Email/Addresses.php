<?php

namespace WolfSellers\Bopis\Block\Order\Email;

use Magento\Framework\View\Element\Template;
use Magento\Sales\Api\OrderRepositoryInterface;
use WolfSellers\Bopis\Model\ResourceModel\AbstractBopisCollection;

class Addresses extends \Magento\Framework\View\Element\Template
{
    /** @var OrderRepositoryInterface  */
    private OrderRepositoryInterface $orderRepository;

    /**
     * @param Template\Context $context
     * @param OrderRepositoryInterface $orderRepository
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        OrderRepositoryInterface $orderRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->orderRepository =  $orderRepository;
    }

    /**
     * @return array|mixed|null
     */
    public function getOrder()
    {
        $orderId = (int)$this->getData('order_id');

        if ($orderId) {
            $order = $this->orderRepository->get($orderId);
            $this->setData('order', $order);
        }

        return $this->getData('order');
    }

    /**
     * @return Addresses
     */
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
    protected function prepareBlockData(): void
    {
        $order = $this->getOrder();

        $this->addData(
            [
                'is_pickup'  => boolval(($order->getShippingMethod() == AbstractBopisCollection::PICKUP_SHIPPING_METHOD))
            ]
        );
    }
}

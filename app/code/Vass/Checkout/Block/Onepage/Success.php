<?php

namespace Vass\Checkout\Block\Onepage;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\View\Element\Template;
use Magento\Sales\Api\OrderRepositoryInterface;

class Success extends \Magento\Checkout\Block\Onepage\Success
{
    protected $orderRepository;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Framework\App\Http\Context $httpContext,
        OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        array $data = []
    ) {
        parent::__construct($context, $checkoutSession, $orderConfig, $httpContext, $data);
        $this->_isScopePrivate = true;
        $this->orderRepository = $orderRepository;
        $this->timezone = $timezone;
    }

    public function getOrderInfo()
    {
        $orderId = $this->_checkoutSession->getLastOrderId();
        if ($orderId) {
            return $this->orderRepository->get($orderId);
        }
        return false;
    }

    public function getCreatedOrder($order){
        $created = $order->getCreatedAt();
        $created = $this->timezone->date(new \DateTime($created));
        $dateAsString = $created->format('d/m/Y H:i:s');

        return $dateAsString;
    }
}

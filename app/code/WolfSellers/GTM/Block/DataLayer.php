<?php
namespace WolfSellers\GTM\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;
use Psr\Log\LoggerInterface;

class DataLayer extends Template
{
    protected $registry;
    protected $logger;

    public function __construct(
        Template\Context $context,
        Registry $registry,
        LoggerInterface $logger,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->logger = $logger;
        parent::__construct($context, $data);
    }

    public function getAddToCartData()
    {
        $data = $this->registry->registry('gtm_add_to_cart_data');
        $this->logger->debug('DataLayer Block: AddToCart Data: ' . json_encode($data));
        return $data;
    }

    public function getBeginCheckoutData()
    {
        $data = $this->registry->registry('gtm_begin_checkout_data');
        $this->logger->debug('DataLayer Block: BeginCheckout Data: ' . json_encode($data));
        return $data;
    }

    public function getOrderSuccessData()
    {
        $data = $this->registry->registry('gtm_order_data');
        $this->logger->debug('DataLayer Block: OrderSuccess Data: ' . json_encode($data));
        return $data;
    }
}

<?php

namespace WolfSellers\Bopis\Helper;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class SuccessHelper extends AbstractHelper
{
    private Session $checkoutSession;

    public function __construct(
        Context $context,
        Session $checkoutSession
    )
    {
        parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
    }

    public function isBopis($string){
        return strpos(strtolower($string), 'bopis') !== false;
    }

    public function getIsBopis(){
        $order = $this->checkoutSession->getLastRealOrder();
        return $this->isBopis($order->getShippingMethod());
    }

}

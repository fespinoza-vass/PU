<?php
namespace Izipay\Core\Block;

use \Izipay\Core\Helper\Data as IzipayHelper;
use \Magento\Framework\View\Element\Template\Context;
use \Izipay\Core\Model\IzipayFactory;
use \Magento\Framework\View\Element\Template;
use \Izipay\Core\Logger\Logger;
use Magento\Framework\Session\SessionManagerInterface;


class Payment extends Template
{
    protected $_helper;
    protected $_logger;
    protected $_sessionManager;
    
    public function __construct(
        IzipayHelper $_helper,
        Logger $_logger,
        Context $context,
        SessionManagerInterface $session
    ) {
        $this->_helper = $_helper;
        $this->_logger = $_logger;
        $this->_sessionManager = $session;

        parent::__construct($context);
    }

    public function getOrder()
    {   
        return $this->getData('order');
    }

    public function getIzipayResponse()
    {   
        return $this->getData('izipay_response');
    }

    public function getSessionData()
    {
        return $this->_sessionManager->getData();
    }

    public function getCustomerId()
    {
        return $this->getOrder()->getCustomerId();
    }

    public function formatPaymentAmount($amount){
        return $this->_helper->formatPaymentAmount($amount);
    }

    public function orderCreatedWithIzipayPayment(){
        return $this->getOrder()->getPayment()->getMethod() == 'izipay';
    }

    
}
                                                                                                                                                                        
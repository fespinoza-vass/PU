<?php
namespace Izipay\Core\Block;

use \Izipay\Core\Helper\Data as IzipayHelper;
use \Magento\Framework\App\Http\Context as HttpContext;
use Magento\Customer\Model\Context as CustomerContext;
use \Magento\Framework\View\Element\Template\Context;
use \Magento\Framework\View\Element\Template;
use \Magento\Sales\Model\Order\Config;
use \Magento\Sales\Model\OrderFactory;
use \Izipay\Core\Logger\Logger;

class Confirmation extends Template
{
	protected $_orderFactory;
	protected $_helper;
	protected $_logger;
	protected $_orderConfig;
    protected $_httpContext;
    
    public function __construct(
    	OrderFactory $_orderFactory,
        IzipayHelper $_helper,
        HttpContext $_httpContext,
        Logger $_logger,
        Config $_orderConfig,
        Context $context
    ) {
        $this->_orderFactory = $_orderFactory;
        $this->_orderConfig  = $_orderConfig;
        $this->_httpContext  = $_httpContext;
        $this->_helper = $_helper;
        $this->_logger = $_logger;

        parent::__construct($context);
    }

	public function formatPaymentAmount($amount){
		return $this->_helper->formatPaymentAmount($amount);
	}

	public function getPaymentStatuses($statusCode){
		return $this->_helper->getPaymentStatuses($statusCode);		
	}

	public function getOrder($incrementId){
		return $this->_orderFactory->create()->loadByIncrementId($incrementId);
	}

	protected function isVisible($order)
    {
        return !in_array(
            $order->getStatus(),
            $this->_orderConfig->getInvisibleOnFrontStatuses()
        );
    }

	public function getCanViewOrder($order)
    {
    	$this->_logger->info('DEBUGING');
    	$this->_logger->info($this->_httpContext->getValue(CustomerContext::CONTEXT_AUTH));
    	$this->_logger->info($this->isVisible($order));
        return $this->_httpContext->getValue(CustomerContext::CONTEXT_AUTH)
            && $this->isVisible($order);
    }

	public function getViewOrderUrl($order){
		return $this->getUrl(
            'sales/order/view/',
            ['order_id' => $order->getEntityId()]
        );
	}
}
<?php
namespace Izipay\Core\Controller\Payment;

use \Izipay\Core\Helper\Data as IzipayHelper;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Checkout\Model\Session;
use Magento\Sales\Model\Order;
use \Izipay\Core\Logger\Logger;

class Index extends Action
{
	protected $_logger;
	private $_checkoutSession;

	public function __construct(
		IzipayHelper $_helper,
		Session $checkoutSession,
		Logger $_logger,
		Context $context
	)
	{
		$this->_checkoutSession = $checkoutSession;
		$this->_logger = $_logger;
		$this->_helper = $_helper;

		return parent::__construct($context);
	}

	public function formatPaymentAmount($amount){
		return $this->_helper->formatPaymentAmount($amount);
	}

	public function execute()
	{	
        $order = $this->_checkoutSession->getLastRealOrder();
		$quote = $this->_checkoutSession->getQuote();
		
        $page = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $block = $page->getLayout()->getBlock('order.izipay.payment');
        $block->setData('order', $order);
		

        if(!$order || !$order->getId()){
        	$this->_redirect('/');
        }

		//TODO: hay que obtener el status payment del config y setearlo.
        $order->setStatus('pending_payment');
        $order->save();

        return $page;
	}
}

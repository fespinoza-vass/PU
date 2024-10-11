<?php
namespace Izipay\Core\Controller\Payment;

use \Izipay\Core\Helper\Data as IzipayHelper;
use Magento\Framework\Controller\ResultInterface;
use \Izipay\Core\Model\IzipayFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use \Izipay\Core\Logger\Logger;

class Confirmation extends Action
{
	protected $_izipayFactory;
	protected $_logger;

	public function __construct(
		IzipayFactory $_izipayFactory,
		IzipayHelper $_helper,
		Logger $_logger,
		Context $context
	)
	{
		$this->_izipayFactory = $_izipayFactory;
		$this->_logger = $_logger;
		$this->_helper = $_helper;

		return parent::__construct($context);
	}

	public function execute()
	{
		//$data = $this->getRequest()->getParams();
        //$izipayData = $this->_izipayFactory->create()->load(base64_decode($data['id']));

        $page = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $block = $page->getLayout()->getBlock('order.izipay.confirmation');
        //$block->setData('izipay', $izipayData);

        return $page;
	}
}

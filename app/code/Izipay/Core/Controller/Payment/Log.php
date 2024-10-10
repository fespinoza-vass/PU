<?php
 
namespace Izipay\Core\Controller\Payment;
 
use \Izipay\Core\Helper\Data as IzipayHelper;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use \Izipay\Core\Logger\Logger;
use Magento\Checkout\Model\Session;
use Magento\Quote\Model\QuoteIdMaskFactory;
use \Izipay\Core\Model\IzipayFactory;
use \Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Customer\Model\Session as CustomerSession;

class Log extends Action
{
 
    private $resultJsonFactory;
    protected $_logger;
    protected $_helper;
    private $_checkoutSession;
    private $_quoteIdMaskFactory;
    protected $_izipayFactory;
    protected $_date;
    protected $_customerSession;

    public function __construct(
        IzipayHelper $_helper,
        JsonFactory $resultJsonFactory, 
        Context $context,
        Logger $_logger,
		Session $checkoutSession,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        IzipayFactory $_izipayFactory,
        DateTime $_date,
        CustomerSession $customerSession)
    {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_logger = $_logger;
        $this->_helper = $_helper;
        $this->_date = $_date;

        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
        $this->_quoteIdMaskFactory = $quoteIdMaskFactory;

        $this->_izipayFactory = $_izipayFactory;
    }
 
    public function execute()
    {
        $post = $this->getRequest()->getPostValue();
        if ($post) {
            $this->_logger->debug('GUARDAR LOG');

            $resultJson = $this->resultJsonFactory->create();
            
            $cartId       = $post['cartId'];
            if (!$this->_customerSession->isLoggedIn())
                $cartId = $this->_quoteIdMaskFactory->create()->load($cartId, 'masked_id')->getQuoteId();

            // Guardar Log
            $izipayModel  = $this->_izipayFactory->create();
			$izipayModel->setData([
                'cart_id' => $cartId,
                'order_number' => $post["order_number"],
                'type_request' => $post["type"],
                'request' => $post["request"],
                'response' => $post["response"],
                'payment_status' => $post["status"],
				'created_at' => $this->_date->gmtDate('Y-m-d H:i:s'),
                'updated_at' => $this->_date->gmtDate('Y-m-d H:i:s')
			])->save();

            return $resultJson->setData(
                [
                    "success" => true
                ]
            );
        }
        
    }
}
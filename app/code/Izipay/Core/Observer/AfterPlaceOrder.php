<?php

namespace Izipay\Core\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\DataObject;
use \Izipay\Core\Logger\Logger;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Sales\Model\Order;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\Controller\ResultFactory;
use \Izipay\Core\Helper\Data;
use Magento\Checkout\Model\Session;

class AfterPlaceOrder implements ObserverInterface {

    protected $_order;
    protected $_logger;
    protected $_actionFlag;
    protected $_response;
    protected $_redirect;
    protected $_url;
    protected $_request;
    protected $_resultFactory;
    protected $_helper;
    protected $_checkoutSession;

    public function __construct(
    Order $_order,
    RedirectInterface $_redirect,
    ActionFlag $actionFlag,
    Logger $_logger,
    ResponseInterface $response,
    RequestInterface $_request,
    UrlInterface $_url,
    ResultFactory $_resultFactory,
    Data $_helper,
    Session $checkoutSession
    ) {
        $this->_order = $_order;
        $this->_logger = $_logger;
        $this->_redirect = $_redirect;
        $this->_response = $response;
        $this->_request = $_request;
        $this->_actionFlag = $actionFlag;
        $this->_url = $_url;
        $this->_resultFactory = $_resultFactory;
        $this->_helper = $_helper;
        $this->_checkoutSession = $checkoutSession;
    }

    public function execute(Observer $observer) {

        $order= $observer->getEvent()->getOrder();

        if ($order->getPayment()->getMethod() == 'izipay') {
            $order_full = $this->_order->loadByIncrementId($order->getIncrementId());

            $page = $this->_resultFactory->create(ResultFactory::TYPE_PAGE);    
            $block = $page->getLayout()->getBlock('order.izipay.payment');
            $block->setData('order', $order);

            // obtener trama de respuesta
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $orderNumber = $order_full->getIzipayOrderNumber();
            $collection = $objectManager->create('Izipay\Core\Model\Izipay')->getCollection()->addFieldToFilter('order_number', $orderNumber)->addFieldToFilter('type_request', "Form Izipay Response"); 
            $izipay_log = $collection->getFirstItem();

            $this->_logger->debug("JSON RESPONSssE:".$izipay_log->getId());
            $this->_logger->debug("JSON RESPONSssE:".$izipay_log->getOrderNumber());
            $this->_logger->debug("JSON RESPONSssE:".$izipay_log->getResponse());
            
            if(!is_null($izipay_log->getResponse())){
                $json_response = json_decode($izipay_log->getResponse(), true);

                $message_payment = $this->_helper->getPaymentStatuses($order_full->getIzipayPaymentCodeResponse());
                
                $json_response["code"] = $order_full->getIzipayPaymentCodeResponse();
                $json_response["messageUser"] = $message_payment;
    
                $block->setData('izipay_response', $json_response);
    
                if ($order_full->getIzipayPaymentCodeResponse() == "00") {
                    $procesing_status = $this->_helper->getProcessingStatus();
                    $order_full->setStatus($procesing_status);
                } else {
                    $pending_payment_status = $this->_helper->getPendingPaymentStatus();
                    $order_full->setStatus($pending_payment_status);
                }
    
                //Comentario en el pedido
                $history = $order_full->addStatusHistoryComment("Response Izipay: ".$message_payment." <br /> Response Code:".$order_full->getIzipayPaymentCodeResponse());
                $history->save();
                $order_full->save();
            }


            return $page;
        }
        return $this;
    }
}
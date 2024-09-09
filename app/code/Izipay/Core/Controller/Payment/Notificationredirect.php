<?php
namespace Izipay\Core\Controller\Payment;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use \Magento\Framework\Controller\Result\JsonFactory;
use \Magento\Framework\Stdlib\DateTime\DateTime;
use \Izipay\Core\Model\NotificationFactory;
use \Izipay\Core\Helper\Data;
use \Izipay\Core\Logger\Logger;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\UrlInterface;

use Magento\Checkout\Model\Session;

use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;

use Magento\Framework\Message\ManagerInterface;
use Izipay\Core\Model\IzipayFactory;
use Magento\Quote\Model\QuoteManagement;

use Magento\Sales\Api\OrderRepositoryInterface;


class Notificationredirect extends Action implements CsrfAwareActionInterface
{
	/** @var \Magento\Framework\Controller\Result\JsonFactory */
    protected $_jsonResultFactory;
    protected $_notificationFactory;
    protected $_helper;
    protected $_logger;
    protected $_date;
    protected $_response;
    protected $_url;
    protected $_checkoutSession;
    protected $_messageManager;
    protected $_izipayFactory;
    protected $_quoteManagement;
    protected $_orderRepository;

    public function __construct(
        NotificationFactory $_notificationFactory,
        JsonFactory $_jsonResultFactory,
        Context $context,
        DateTime $_date,
        Data $_helper,
        Logger $_logger,
        ResponseInterface $_response,
        UrlInterface $_url,
        Session $checkoutSession,
        ManagerInterface $_messageManager,
        IzipayFactory $_izipayFactory,
        QuoteManagement $quoteManagement,
        OrderRepositoryInterface $_orderRepository
    ) {
        parent::__construct($context);
        $this->_notificationFactory = $_notificationFactory;
        $this->_jsonResultFactory = $_jsonResultFactory;
        $this->_helper = $_helper;
        $this->_logger = $_logger;
        $this->_date = $_date;
        $this->_response = $_response;
        $this->_url = $_url;
        $this->_checkoutSession = $checkoutSession;
        $this->_messageManager = $_messageManager;
        $this->_izipayFactory = $_izipayFactory;
        $this->_quoteManagement = $quoteManagement;
        $this->_orderRepository = $_orderRepository;
    }

    public function execute()
    {
    	$params = $this->getRequest()->getParams();
		$isActive = $this->_helper->getActive();

		if($isActive && isset($params["code"])) {

            // Obtiene el quote
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $orderNumber = $params["orderNumber"];
            $collection = $objectManager->create('Magento\Quote\Model\Quote')->getCollection()->addFieldToFilter('izipay_order_number', $orderNumber); 
            $quote = $collection->getFirstItem();

            // Inserta en tabla de log.
            $izipayModel  = $this->_izipayFactory->create();
			$izipayModel->setData([
                'cart_id' => $quote->getEntityId(),
                'order_number' => $params["orderNumber"],
                'type_request' => "Form Izipay Response",
                'request' => "",
                'response' => json_encode($params),
                'payment_status' => $params["code"],
				'created_at' => $this->_date->gmtDate('Y-m-d H:i:s'),
                'updated_at' => $this->_date->gmtDate('Y-m-d H:i:s')
			])->save();

            if ($params["code"]=="00") {
                $orderId = $this->_quoteManagement->placeOrder($quote->getEntityId());
                if ( $orderId ) {
                    $order = $this->_orderRepository->get($orderId);
                    $order->setIzipayPaymentCodeResponse($params["code"]);
                    $order->save();

                    // Redireccionamos al success                                
                    $this->_checkoutSession->setLastOrderId($order->getId());
                    $this->_checkoutSession->setLastRealOrderId($order->getIncrementId());
                    $this->_checkoutSession->setLastOrderStatus($order->getStatus());
                    //$this->_checkoutSession->setLastQuoteId($order->getQuoteId());
                    //$this->_checkoutSession->setLastSuccessQuoteId($order->getQuoteId());
                    //$this->_messageManager->addSuccess("ORDEN PAGADA CON IZIPAY");

                    //$this->_checkoutSession->clearQuote();

                    $redirectionUrl = $this->_url->getUrl('checkout/onepage/success');
                    $this->_redirect->redirect($this->_response, $redirectionUrl);

                } else {
                    $this->_logger->debug("Ocurrió un error creando el pedido en Notificationredirect Izipay");
                }
            } else {
                $this->_checkoutSession->setQuoteId($quote->getEntityId());
                //$redirectionUrl = $this->_url->getUrl('checkout/cart/index');
                $redirectionUrl = $this->_url->getUrl('checkout', ['_fragment' => 'shipping']);
                $this->_redirect->redirect($this->_response, $redirectionUrl);
            }
            
		} else {
            $this->_checkoutSession->setQuoteId($quote->getEntityId());
            //$redirectionUrl = $this->_url->getUrl('checkout/cart/index');
            $redirectionUrl = $this->_url->getUrl('checkout', ['_fragment' => 'shipping']);
            $this->_redirect->redirect($this->_response, $redirectionUrl);
        }

        /** @var \Magento\Framework\Controller\Result\Json $result */
        $result = $this->_jsonResultFactory->create();
        $result->setHttpResponseCode(\Magento\Framework\Webapi\Response::HTTP_OK);
        return $result;
    }


    /**
     * @inheritDoc
     */
    public function createCsrfValidationException(
        RequestInterface $request
    ): ?InvalidRequestException {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}

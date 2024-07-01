<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace PechoSolutions\Visanet\Block\Onepage;

use Magento\Customer\Model\Context;
use Magento\Sales\Model\Order;
use Magento\Directory\Model\Currency;
/**
 * One page checkout success page
 *
 * @api
 * @since 100.0.2
 */
class Success extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    protected $_orderConfig;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var \PechoSolutions\Visanet\Helper\Data
     */
    protected $_helperConfig;

    /**
     * @var\Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManagerInterface;

    /**
     * @var\Magento\Catalog\Model\Product
     */
    protected $_product;

        /**
     * @var\Magento\Catalog\Model\Product
     */
    protected $_productRepository;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    protected $currency;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \PechoSolutions\Visanet\Helper\Data $helperConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        Currency $currency,       
        \Magento\Catalog\Model\Product $product,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_checkoutSession = $checkoutSession;
        $this->_orderConfig = $orderConfig;
        $this->_isScopePrivate = true;
        $this->httpContext = $httpContext;
        $this->_orderFactory=$orderFactory;
        $this->_helperConfig=$helperConfig;
        $this->_storeManagerInterface=$storeManagerInterface;
        $this->_product=$product;
        $this->_productRepository=$productRepository;
        $this->currency = $currency;
    }


    /**
     * Return Total
     *
     * @return number
     */
    public function getOrderGrandTotal()
    {
        $order = $this->_checkoutSession->getLastRealOrder();
        return $order->getGrandTotal();
    }

    /**
     * Get Currency Code
     *
     * @return number
     */
    public function getCurrencyCode()
    {
        // $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/visanew.log');
        // $logger = new \Zend\Log\Logger();
        // $logger->addWriter($writer);
	$writer = new \Zend_Log_Writer_Stream(BP . '/var/log/visanew.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info("Currency Code");
        $logger->info($this->_storeManagerInterface->getStore()->getBaseCurrencyCode());
        return $this->_storeManagerInterface->getStore()->getBaseCurrencyCode();        
    }


    /**
     * Get Currency
     *
     * @return number
     */
    public function getCurrency()
    {
        // $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/visanew.log');
        // $logger = new \Zend\Log\Logger();
        // $logger->addWriter($writer);
	$writer = new \Zend_Log_Writer_Stream(BP . '/var/log/visanew.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info("Currency jp");
        $logger->info($this->currency->getCurrencySymbol());
        return $this->currency->getCurrencySymbol();        
    }

     /**
     * Return Order Items
     *
     * @return array
     */
    public function getOrderAllVisibleItems()
    {
        $order = $this->_checkoutSession->getLastRealOrder();
        return $order->getAllVisibleItems();
    }

    /**
     * Return Billing Address
     *
     * @return array
     */
    public function getOrderData()
    {
        /*$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/visanew.log');
        // $logger = new \Zend\Log\Logger();
        // $logger->addWriter($writer); 

	$writer = new \Zend_Log_Writer_Stream(BP . '/var/log/visanew.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info("method getOrderBillingAddress");
        $logger->info($this->getOrderId());*/
       
        //$logger->info("get order data");
        return $this->_orderFactory->create()->loadByIncrementId($this->getOrderId());       
    }
    
  
   /**
     * Return Get Media Directory
     *
     * @return string
     */
    public function getMediaDirectory()
    {
        return  $this->_storeManagerInterface->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);     
    }

       /**
     * Return Get Helper Config Data
     *
     * @return string
     */
    public function getHelperConfig($config)
    {
        return  $this->_helperConfig->getConfig($config);     
    }

    /**
     * Return Get Product
     *
     * @return string
     */
    public function getProduct($productId)
    {
        return  $this->_product->load($productId);     
    }
    /**
     * Return Get Real Product
     *
     * @return string
     */
    public function getRealProduct($SKU)
    {
        return  $this->_productRepository->get($SKU);     
    }
    /**
     * Return Get Quote Data
     *
     * @return array
     */
    public function getQuoteData()
    {
        return  $this->_checkoutSession->getQuote()->getData();     
    }
    /**
     * Return Get Quote Items
     *
     * @return array
     */
    public function getQuoteItems()
    {
        return  $this->_checkoutSession->getQuote()->getAllVisibleItems();     
    }

     /**
     * Return Get Quote Billing Address
     *
     * @return array
     */
    public function getQuoteBillingAddress()
    {
        return  $this->_checkoutSession->getQuote()->getBillingAddress()->getData();     
    }
 /**
     * Return Get Quote Shipping Address
     *
     * @return array
     */
    public function getQuoteShippingAddress()
    {
        return  $this->_checkoutSession->getQuote()->getShippingAddress()->getData();     
    }
 /**
     * Return Get Quote ID
     *
     * @return string
     */
    public function getQuoteId()
    {
        return  $this->_checkoutSession->getQuote()->getId();     
    }

    /**
     * Render additional order information lines and return result html
     *
     * @return string
     */
    public function getAdditionalInfoHtml()
    {
        return $this->_layout->renderElement('order.success.additional.info');
    }

    /**
     * Initialize data and prepare it for output
     *
     * @return string
     */
    protected function _beforeToHtml()
    {
        $this->prepareBlockData();
        return parent::_beforeToHtml();
    }

    /**
     * Prepares block data
     *
     * @return void
     */
    protected function prepareBlockData()
    {
        $order = $this->_checkoutSession->getLastRealOrder();

        $this->addData(
            [
                'is_order_visible' => $this->isVisible($order),
                'view_order_url' => $this->getUrl(
                    'sales/order/view/',
                    ['order_id' => $order->getEntityId()]
                ),
                'print_url' => $this->getUrl(
                    'sales/order/print',
                    ['order_id' => $order->getEntityId()]
                ),
                'can_print_order' => $this->isVisible($order),
                'can_view_order'  => $this->canViewOrder($order),
                'order_id'  => $order->getIncrementId()
            ]
        );
    }

    /**
     * Is order visible
     *
     * @param Order $order
     * @return bool
     */
    protected function isVisible(Order $order)
    {
        return !in_array(
            $order->getStatus(),
            $this->_orderConfig->getInvisibleOnFrontStatuses()
        );
    }

    /**
     * Can view order
     *
     * @param Order $order
     * @return bool
     */
    protected function canViewOrder(Order $order)
    {
        return $this->httpContext->getValue(Context::CONTEXT_AUTH)
            && $this->isVisible($order);
    }

    /**
     * @return string
     * @since 100.2.0
     */
    public function getContinueUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }
}

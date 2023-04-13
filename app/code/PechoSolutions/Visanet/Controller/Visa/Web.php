<?php

namespace PechoSolutions\Visanet\Controller\Visa;
use Magento\Sales\Model\Order;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;

use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;
//Remove implements HttpPostActionInterface, CsrfAwareActionInterface for version lower than 2.3
class Web extends \Magento\Framework\App\Action\Action implements HttpPostActionInterface, CsrfAwareActionInterface {

    private $encryptor;
    protected $registry;
    protected $checkoutSession;
    protected $cartManagement;
    protected $paymentMethod;
    protected $paymentMethodManagement;
    protected $cookieManager;
    protected $cookieMetadataFactory;
    protected $helperData;
    protected $customerSession;
    protected $orderRepository;
    protected $storeManager;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Framework\Registry $registry,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Api\CartManagementInterface $cartManagement,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \PechoSolutions\Visanet\Helper\Data $helperData,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        StoreManagerInterface $storeManager
    )
    {
        parent::__construct($context);
        $this->encryptor = $encryptor;
        $this->registry = $registry;
        $this->checkoutSession = $checkoutSession;
        $this->cartManagement = $cartManagement;
        $this->paymentMethod = $paymentMethod;
        $this->paymentMethodManagement = $paymentMethodManagement;

        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->helperConfig = $helperData;
        $this->customerSession = $customerSession;
        $this->orderRepository = $orderRepository;
        $this->storeManager = $storeManager;
    }


    public function getCheckoutSession()
    {
        return $this->checkoutSession;
    }

    public function execute() {
        $this->cookieManager->deleteCookie( 'checkout_message');
        $cookieExpiration = 20 * 24 * 60 * 60;//in seconds
        $publicCookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
                    ->setDuration($cookieExpiration)
                    ->setPath('/');
        if (isset($_POST['transactionToken'])){

            if(!isset($_SESSION))
            {
                session_start();
            }

            $quote = $this->checkoutSession->getQuote();

            if( $quote->getId()){
                try {

                    $quote_id = $quote->getId();

                    $transactionToken = $_POST['transactionToken'];
                    $sessionToken = $this->getCheckoutSession()->getSessionToken();
                    $sessionKey = $this->getCheckoutSession()->getSessionKey();

                    if (empty($sessionKey) || empty($sessionToken)){
                        throw new \Exception("SessionKey or SessionToken not valid.");
                    }

                    $this->registry->register('transactionToken', $transactionToken);
                    $this->registry->register('sessionToken', $sessionToken);
                    $this->registry->register('sessionKey', $sessionKey);

                    $debug = $this->helperConfig->getConfig('payment/visanet_pay/visanetConfiguracion/debug');
                    $ambiente = ($debug == '1') ? 'dev' : 'prd';

                    $currencyCode=$this->storeManager->getStore()->getBaseCurrencyCode();
                    $merchant_id="";
                    if($currencyCode=="USD")
                    {
                        $merchant_id=$this->helperConfig->getConfig('payment/visanet_pay/visanetConfiguracion/merchant_id_dollar');
                    }elseif($currencyCode=="PEN"){
                        $merchant_id=$this->helperConfig->getConfig('payment/visanet_pay/visanetConfiguracion/merchant_id');
                    }

                    /*$merchant_id = $this->helperConfig->getConfig('payment/visanet_pay/visanetConfiguracion/merchant_id');*/

                    $access_key = $this->encryptor->decrypt($this->helperConfig->getConfig('payment/visanet_pay/visanetConfiguracion/public_key'));
                    $SecretAccessKey = $this->encryptor->decrypt($this->helperConfig->getConfig('payment/visanet_pay/visanetConfiguracion/private_key'));

                    $this->paymentMethod->setMethod('visanet_pay');
                    $this->paymentMethodManagement->set($quote->getId(), $this->paymentMethod);

                    if(!$this->customerSession->isLoggedIn())
                    {
                        $guestEmail= $this->checkoutSession->getGuestEmail();
                        if($guestEmail===''){
                             $guestEmail= $quote->getBillingAddress()->getEmail();
                        }
                        $quote->setCustomerId(null);
                        $quote->setCustomerEmail( $guestEmail);
                        $quote->setCustomerIsGuest(true);
                        $quote->setCustomerGroupId(\Magento\Customer\Api\Data\GroupInterface::NOT_LOGGED_IN_ID);
                    }

                    $orderId = $this->cartManagement->placeOrder($quote->getId());
                    $order = $this->orderRepository->get( $orderId);
                    if ($order) {

                        $orderState = Order::STATE_PROCESSING;
                        $order->setStatus(Order::STATE_COMPLETE);
                        $order->save();
                    }

                    $this->messageManager->addSuccess('Compra exitosa con Visa');
                    $this->_redirect('checkout/onepage/success');
                } catch (\Exception $e) {
                    $this->messageManager->addWarning($e->getMessage());
                    $this->_redirect('visanet/onepage/error');
                }
            }
            else{
                $this->cookieManager->setPublicCookie(
                    'checkout_message',
                    'Su carrito no es valido',
                    $publicCookieMetadata
                );
                $this->messageManager->addWarning('Su carrito no es valido');
                $this->_redirect('visanet/onepage/error');
            }
        }
        else{
            $this->cookieManager->setPublicCookie(
                'checkout_message',
                'Token no recibido',
                $publicCookieMetadata
            );
            $this->messageManager->addWarning('Token no recibido');
            $this->_redirect('visanet/onepage/error');
        }
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

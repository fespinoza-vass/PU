<?php

namespace PechoSolutions\Visanet\Controller\Visa;

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;

use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
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
        \Magento\Customer\Model\Session $customerSession
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

            $quote = $this->checkoutSession->getQuote();

            if(!isset($_SESSION))
            {
                session_start();
            }

            if( $quote->getId()){
                try {
                    $transactionToken = $_POST['transactionToken'];
                    $sessionToken = $this->getCheckoutSession()->getSessionToken();
                    $sessionKey = $this->getCheckoutSession()->getSessionKey();

                    $this->registry->register('transactionToken', $transactionToken);
                    $this->registry->register('sessionToken', $sessionToken);
                    $this->registry->register('sessionKey', $sessionKey);
                    $this->paymentMethod->setMethod('visanet_pay');
                    $this->paymentMethodManagement->set($quote->getId(), $this->paymentMethod);

                    if(!$this->customerSession->isLoggedIn())
                    {
                        $guestEmail = ($quote->getShippingAddress()->getEmail() != null) ? $quote->getShippingAddress()->getEmail() :  $quote->getBillingAddress()->getEmail();
                        $quote->setCustomerId(null);
                        $quote->setCustomerEmail( $guestEmail);
                        $quote->setCustomerIsGuest(true);
                        $quote->setCustomerGroupId(\Magento\Customer\Api\Data\GroupInterface::NOT_LOGGED_IN_ID);
                    }

                    $orderId = $this->cartManagement->placeOrder($quote->getId());

                    $this->messageManager->addSuccess('Compra completada con tu tarjeta bancaría');
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

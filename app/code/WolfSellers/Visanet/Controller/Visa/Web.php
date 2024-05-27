<?php

namespace WolfSellers\Visanet\Controller\Visa;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Api\PaymentMethodManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Store\Model\StoreManagerInterface;
use PechoSolutions\Visanet\Helper\Data;

class Web extends \PechoSolutions\Visanet\Controller\Visa\Web
{
    /**
     * @var EncryptorInterface
     */
    protected EncryptorInterface $_encryptor;

    /**
     * @param Context $context
     * @param EncryptorInterface $encryptor
     * @param Registry $registry
     * @param Session $checkoutSession
     * @param CartManagementInterface $cartManagement
     * @param PaymentInterface $paymentMethod
     * @param PaymentMethodManagementInterface $paymentMethodManagement
     * @param CookieManagerInterface $cookieManager
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param Data $helperData
     * @param \Magento\Customer\Model\Session $customerSession
     * @param OrderRepositoryInterface $orderRepository
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context                          $context,
        EncryptorInterface               $encryptor,
        Registry                         $registry,
        Session                          $checkoutSession,
        CartManagementInterface          $cartManagement,
        PaymentInterface                 $paymentMethod,
        PaymentMethodManagementInterface $paymentMethodManagement,
        CookieManagerInterface           $cookieManager,
        CookieMetadataFactory            $cookieMetadataFactory,
        Data                             $helperData,
        \Magento\Customer\Model\Session  $customerSession,
        OrderRepositoryInterface         $orderRepository,
        StoreManagerInterface            $storeManager
    )
    {
        parent::__construct(
            $context,
            $encryptor,
            $registry,
            $checkoutSession,
            $cartManagement,
            $paymentMethod,
            $paymentMethodManagement,
            $cookieManager,
            $cookieMetadataFactory,
            $helperData,
            $customerSession,
            $orderRepository,
            $storeManager
        );
        $this->_encryptor = $encryptor;
    }

    /**
     * @return void
     * @throws CookieSizeLimitReachedException
     * @throws FailureToSendException
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws \Zend_Log_Exception
     */
    public function execute()
    {
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/visanew.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info("Web Capture");

        $this->cookieManager->deleteCookie('checkout_message');
        $cookieExpiration = 20 * 24 * 60 * 60;//in seconds
        $publicCookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
            ->setDuration($cookieExpiration)
            ->setPath('/');
        $logger->info("Cookie Created");

        if (isset($_POST['transactionToken'])) {
            $logger->info("Transaction Token exit");

            if (!isset($_SESSION)) {
                session_start();
            }

            $logger->info("Session started");

            $quote = $this->checkoutSession->getQuote();

            $logger->info("Get customer exits:" . isset($customer));
            $logger->info("Get quote info ");

            if ($quote->getId()) {
                $logger->info("Get quote id: " . $quote->getId());

                try {
                    $quote_id = $quote->getId();

                    $transactionToken = $_POST['transactionToken'];
                    $sessionToken = $this->getCheckoutSession()->getSessionToken();
                    $sessionKey = $this->getCheckoutSession()->getSessionKey();

                    $this->registry->register('transactionToken', $transactionToken);
                    $this->registry->register('sessionToken', $sessionToken);
                    $this->registry->register('sessionKey', $sessionKey);

                    $logger->info("Session registered");

                    $debug = $this->helperConfig->getConfig('payment/visanet_pay/visanetConfiguracion/debug');
                    $ambiente = ($debug == '1') ? 'dev' : 'prd';

                    $logger->info("Get debug:" . $debug);
                    $logger->info("ambiente:" . $ambiente);

                    $currencyCode = $this->storeManager->getStore()->getBaseCurrencyCode();
                    $merchant_id = "";

                    if ($currencyCode == "USD") {
                        $merchant_id = $this->helperConfig->getConfig('payment/visanet_pay/visanetConfiguracion/merchant_id_dollar');
                    } elseif ($currencyCode == "PEN") {
                        $merchant_id = $this->helperConfig->getConfig('payment/visanet_pay/visanetConfiguracion/merchant_id');
                    }

                    /*$merchant_id = $this->helperConfig->getConfig('payment/visanet_pay/visanetConfiguracion/merchant_id');*/

                    $access_key = $this->_encryptor->decrypt($this->helperConfig->getConfig('payment/visanet_pay/visanetConfiguracion/public_key'));
                    $SecretAccessKey = $this->_encryptor->decrypt($this->helperConfig->getConfig('payment/visanet_pay/visanetConfiguracion/private_key'));

                    $logger->info("Merchant id:" . $merchant_id);
                    $logger->info("Access Key:" . $access_key);
                    $logger->info("Secret Access key:" . $SecretAccessKey);

                    $this->paymentMethod->setMethod('visanet_pay');
                    $logger->info("Set Method");
                    $this->paymentMethodManagement->set($quote->getId(), $this->paymentMethod);
                    $logger->info("Attach method to the payment method");
                    if (!$this->customerSession->isLoggedIn()) {
                        //$guestEmail= $this->checkoutSession->getGuestEmail();
                        $quote->setCustomerId(null);
                        $quote->setCustomerEmail($quote->getBillingAddress()->getEmail());
                        $quote->setCustomerIsGuest(true);
                        $quote->setCustomerGroupId(\Magento\Customer\Api\Data\GroupInterface::NOT_LOGGED_IN_ID);
                        $logger->info("email:" . $quote->getBillingAddress()->getEmail());
                    }

                    $logger->info("first Name" . $quote->getBillingAddress()->getFirstname());

                    $orderId = $this->cartManagement->placeOrder($quote->getId());
                    $order = $this->orderRepository->get($orderId);
                    if ($order) {
                        $order->setStatus(Order::STATE_PROCESSING);
                        $order->save();
                    }
                    $logger->info("Order Place,method payment  " . $this->paymentMethod->getMethod());

                    $this->messageManager->addSuccess('Compra exitosa con Visa');
                    $this->_redirect('checkout/onepage/success');
                } catch (\Exception $e) {
                    $logger->err($e->getMessage());
                    $logger->err($e->getTraceAsString());
                    $this->messageManager->addWarning($e->getMessage());
                    $this->_redirect('visanet/onepage/error');
                }
            } else {
                $this->cookieManager->setPublicCookie(
                    'checkout_message',
                    'Su carrito no es valido',
                    $publicCookieMetadata
                );

                $this->messageManager->addWarning('Su carrito no es valido');
                $this->_redirect('visanet/onepage/error');
            }
        } else {
            $this->cookieManager->setPublicCookie(
                'checkout_message',
                'Token no recibido',
                $publicCookieMetadata
            );

            $this->messageManager->addWarning('Token no recibido');
            $this->_redirect('visanet/onepage/error');
        }

    }
}


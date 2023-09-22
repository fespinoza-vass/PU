<?php

namespace WolfSellers\Checkout\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Session;
use Magento\Sales\Model\Order;
use Magento\Customer\Model\CustomerFactory;
use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Sales\Api\OrderCustomerManagementInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Customer\Model\Customer;
use Magento\Framework\App\Http\Context;
use Magento\Sales\Model\OrderFactory;
use Magento\Quote\Model\QuoteFactory;
use Magento\Checkout\Model\Cart;


class CreateCountGuest implements ObserverInterface
{

    /**
     * @param \WolfSellers\Checkout\Observer\Session $customerSession
     * @param Order $order
     * @param CustomerFactory $customer
     * @param \WolfSellers\Checkout\Observer\Session $checkoutSession
     * @param StoreManagerInterface $storeManager
     * @param OrderCustomerManagementInterface $orderCustomerService
     * @param EncryptorInterface $encryptor
     * @param CustomerRegistry $customerRegistry
     * @param CustomerRepository $customerRepository
     * @param Customer $customerModel
     * @param Context $http
     * @param OrderFactory $orderFactory
     * @param QuoteFactory $quoteFactory
     * @param Cart $_cart
     */
    public function __construct(
        Session $customerSession,
        Order $order,
        CustomerFactory $customer,
        SessionCheckout $checkoutSession,
        StoreManagerInterface $storeManager,
        OrderCustomerManagementInterface $orderCustomerService,
        EncryptorInterface $encryptor,
        CustomerRegistry $customerRegistry,
        CustomerRepository $customerRepository,
        Customer $customerModel,
        Context $http,
        OrderFactory $orderFactory,
        QuoteFactory $quoteFactory,
        Cart $_cart
    )
    {
        $this->_customerSession = $customerSession;
        $this->_order = $order;
        $this->_customer = $customer;
        $this->_checkoutSession = $checkoutSession;
        $this->_storeManager = $storeManager;
        $this->_orderCustomerService = $orderCustomerService;
        $this->_encryptor = $encryptor;
        $this->_customerRegistry = $customerRegistry;
        $this->_customerRepository = $customerRepository;
        $this->_customerModel = $customerModel;
        $this->_http = $http;
        $this->_orderFactory = $orderFactory;
        $this->quoteFactory = $quoteFactory;
        $this->_cart = $_cart;

    }

    /**
     * @param Observer $observer
     * create guest to customer
     * @return void
     */
    public function execute(Observer $observer)
    {

        $orderId_ = $observer->getEvent()->getOrderIds();
        $orderIds = $orderId_[0];
        $order = $this->_orderFactory->create()->load($orderIds);
        $quote = $this->quoteFactory->create()->load($order->getQuoteId());
        $cpass =$quote->getCustomerPassword();

        if ($cpass ) {
            try {
                $orderId = $orderIds;
                $customer = $this->_customer->create();
                $customer->setWebsiteId($this->_storeManager->getStore()->getWebsiteId());
                $customer->loadByEmail($order->getCustomerEmail());
                if ($order->getId() && !$customer->getId()) {
                    $customer =$this->_orderCustomerService->create($orderId);
                    $passwordHash = $this->_encryptor->getHash($cpass, true);
                    $customerSecure = $this->_customerRegistry->retrieveSecureData($customer->getId());
                    $customerSecure->setRpToken(null);
                    $customerSecure->setRpTokenCreatedAt(null);
                    $customerSecure->setPasswordHash($passwordHash);
                    $customer->setFirstname($quote->getCustomerName());
                    $customer->setLastname($quote->getCustomerApellido());
                    $customer->setCustomAttribute('telefono',$quote->getCustomerTelefono());
                    $customer->setCustomAttribute('identificacion', $quote->getCustomerIdentificacion());
                    $customer->setCustomAttribute('numero_de_identificacion', $quote->getCustomerNumeroDeIdentificacion());
                    $this->_customerRepository->save($customer, $passwordHash);
                    $customer = $this->_customerModel->load($customer->getId());

                    $this->_customerSession->setCustomerAsLoggedIn($customer);
                    $this->_http->setValue('customer_logged_in', true, true);
                }
            } catch (\Throwable $error) {
                $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/create_count.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                $logger->info("CREATE CUSTOMER ACCOUNT FROM CHECKOUT: " . $error->getMessage());
            }
        }
    }
}

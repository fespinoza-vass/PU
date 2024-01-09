<?php

/*
    Este controller sera el responsable de conectar con Ncp para generar el token
    con el precio total del carrito, osea carrito + shipping
*/

namespace Niubiz\Ncp\Controller\Visa;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Store\Model\ScopeInterface;
use mysql_xdevapi\Exception;
use Niubiz\Ncp\Model\Library\Ncp;
use Magento\Store\Model\StoreManagerInterface;

class Hello extends \Magento\Framework\App\Action\Action {

    protected $config;
    private $encryptor;
    protected $checkoutSession;
    protected $resultJsonFactory;
    protected $customerSession;
    protected $storeManager;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Customer\Model\Session $customerSession,
        StoreManagerInterface $storeManager
    )
    {
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
        $this->encryptor = $encryptor;
        $this->checkoutSession = $checkoutSession;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
    }


    public function getCheckoutSession()
    {
        return $this->checkoutSession;
    }


    private function getValueConfig($field, $storeId = null)
    {
        $pathPattern = 'payment/%s/NcpConfiguracion/%s';
        $methodCode = 'Ncp_pay';

        return $this->scopeConfig->getValue(
            sprintf($pathPattern, $methodCode, $field),
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function execute() {

        $quote = $this->checkoutSession->getQuote();
        $grandTotal = $quote->getGrandTotal();

        $Ncp = new Ncp();
        $sessionKey = $Ncp->getGUID();

        $debug = $this->getValueConfig('debug');

        if ($debug == '1'){
            $ambiente = 'dev';
        }else{
            $ambiente = 'prd';
        }

        $currencyCode=$this->storeManager->getStore()->getBaseCurrencyCode();
        $merchantIdbyCurrency="";

        if($currencyCode=="USD")
        {
            $merchantIdbyCurrency=$this->getValueConfig('merchant_id_dollar');
        }elseif($currencyCode=="PEN"){
            $merchantIdbyCurrency=$this->getValueConfig('merchant_id');
        }

        try {

            $publicKey = $this->encryptor->decrypt($this->getValueConfig('public_key'));
            $privateKey = $this->encryptor->decrypt($this->getValueConfig('private_key'));
            $ipClient = $this->getValueConfig('ip_client');

            $securitykey = $Ncp->securitykey($ambiente, $merchantIdbyCurrency, $publicKey,$privateKey);
            $sessionToken = $Ncp->create_token($ambiente,$grandTotal,$securitykey,$merchantIdbyCurrency,$publicKey,$privateKey,$ipClient);

            $this->checkoutSession->setSessionToken($sessionToken);
            $this->checkoutSession->setSessionKey($securitykey);

        } catch (\Exception $e) {
        }

        $response = new \Magento\Framework\DataObject();
        $response->setMonto(
            $grandTotal
        );
        $response->setKey(
            //$sessionKey
            $sessionToken
        );

        $response->setTestMerchantId(
            $this->getValueConfig('merchant_id')
        );

        $response->setTestPublicKey(
            $this->getValueConfig('public_key')
        );

        $response->setTestPrivateKey(
            $this->getValueConfig('private_key')
        );

        return $this->resultJsonFactory->create()
                                       ->setJsonData($response->toJson());

    }
}

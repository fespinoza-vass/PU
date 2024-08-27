<?php

/*
    Este controller sera el responsable de conectar con Visanet para generar el token
    con el precio total del carrito, osea carrito + shipping
*/

namespace Vass\NiubizVisanet\Controller\Visa;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Store\Model\ScopeInterface;
use PechoSolutions\Visanet\Model\Library\Visanet;
use Magento\Store\Model\StoreManagerInterface;

class Hello extends \Magento\Framework\App\Action\Action {
    
    protected $config;
    private $encryptor;   
    protected $checkoutSession;
    protected $resultJsonFactory;   
    protected $customerSession;
    protected $storeManager;
    protected $configNiubiz;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,      
        ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Customer\Model\Session $customerSession,
        StoreManagerInterface $storeManager,
        \Vass\NiubizVisanet\Model\Config $configNiubiz,
    )
    {
        parent::__construct($context);    
        $this->scopeConfig = $scopeConfig;
        $this->encryptor = $encryptor;
        $this->checkoutSession = $checkoutSession;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        $this->_configNiubiz = $configNiubiz;
    }
 

  
    public function execute() {   

        $response = new \Magento\Framework\DataObject(); 

        $quoteTotal = $this->checkoutSession->getQuote()->getGrandTotal();
        $confSetup = $this->_configNiubiz->configurationNiubiz();

        $env = (!empty($confSetup['debug']) && $confSetup['debug'] == '1' ) ? 'dev' : 'prd'; 

        $secret = $this->_configNiubiz->securitykey($env,$confSetup['merchant'],$this->encryptor->decrypt($confSetup['public']),$this->encryptor->decrypt($confSetup['private']));
        $sessionToken =  $this->_configNiubiz->create_token($env,$quoteTotal,$secret,$confSetup['merchant'],$this->encryptor->decrypt($confSetup['public']),$this->encryptor->decrypt($confSetup['private']),$confSetup['ip']);

        $response->setMerchant(
            $confSetup['merchant']
        );

        $response->setToken(
            $sessionToken
        );
        
        return $this->resultJsonFactory->create()
                                       ->setJsonData($response->toJson());

    }
}
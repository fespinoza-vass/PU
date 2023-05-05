<?php

/*
    Este controller sera el responsable de conectar con Visanet para generar el token
    con el precio total del carrito, osea carrito + shipping
*/

namespace PechoSolutions\Visanet\Controller\Visa;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Store\Model\ScopeInterface;
use PechoSolutions\Visanet\Model\Library\Visanet;
   
class Email extends \Magento\Framework\App\Action\Action {
    
    protected $config;
    private $encryptor;   
    protected $checkoutSession;
    protected $resultJsonFactory;   
    
    public function __construct(
        \Magento\Framework\App\Action\Context $context,      
        ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    )
    {
        parent::__construct($context);    
        $this->scopeConfig = $scopeConfig;
        $this->encryptor = $encryptor;
        $this->checkoutSession = $checkoutSession;
        $this->resultJsonFactory = $resultJsonFactory;
      
    }
 


    public function execute() {    
        // $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/visanew.log');
        // $logger = new \Zend\Log\Logger();
        // $logger->addWriter($writer); 
	$writer = new \Zend_Log_Writer_Stream(BP . '/var/log/visanew.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info("Email Controller");
  
        
        $response = new \Magento\Framework\DataObject(); 
        $logger->info("Email".$_POST['email']);
        if (isset($_POST['email'])){
            $this->checkoutSession->setGuestEmail($_POST['email']);
            
            $response->setEmail(true);
        }else{
            $response->setEmail(false);    
        }
        return $this->resultJsonFactory->create()
                                       ->setJsonData($response->toJson());

    }
}
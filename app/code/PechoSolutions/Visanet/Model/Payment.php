<?php


namespace PechoSolutions\Visanet\Model;

use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Framework\Api\ExtensibleDataInterface; 
use Magento\Store\Model\StoreManagerInterface;

class Payment extends AbstractMethod
{
    const CODE = 'visanet_pay';

    protected $_code = self::CODE;
    protected $_isGateway = true;
    protected $_canCapture = true;
    protected $_canCapturePartial = true;
    protected $_canRefund = true;
    protected $_canRefundInvoicePartial = true;
    protected $storeManager;
    protected $_logData;
    //protected $_minAmount;
    //protected $_maxAmount;
    protected $_privateKey;
    protected $_publicKey;
    protected $_merchantId;
    protected $quoteFactory;
    protected $helperConfig;
    protected $visanetManager;
    protected $cartRepository;
    protected $encryptor;
    protected $registry;
    protected $_supportedCurrencyCodes = array('USD', 'PEN');
    protected $_debugReplacePrivateDataKeys = ['number', 'exp_month', 'exp_year', 'cvc', 'source_id'];

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \PechoSolutions\Visanet\Model\LogData $logData,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Framework\App\Filesystem\DirectoryList $dir,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        CartRepositoryInterface $cartRepository,
        \PechoSolutions\Visanet\Helper\Data $helperConfig,
        StoreManagerInterface $storeManager,
        \PechoSolutions\Visanet\Model\Library\Visanet $visanetManager,
        array $data = array()
        
    ) {

        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            null,
            null,
            $data           
        );

        $this->_logData = $logData;
        $this->helperConfig = $helperConfig;
        $this->visanetManager = $visanetManager;
        $this->cartRepository = $cartRepository;
        $this->quoteFactory = $quoteFactory;
        $this->encryptor = $encryptor; 
        $this->registry = $registry;
        $this->storeManager = $storeManager;
    }

    /**
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float $amount
     * @return $this
     * @throws \Magento\Framework\Validator\Exception
     */
    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        // $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/visanew.log');
        // $logger = new \Zend\Log\Logger();
        // $logger->addWriter($writer); 

	$writer = new \Zend_Log_Writer_Stream(BP . '/var/log/visanew.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
    
       if (!$payment->hasAdditionalInformation('token') && trim($this->registry->registry('sessionToken')) == '' ) {
            $this->_logger->error('Payment tokenizer error');
            throw new \Magento\Framework\Validator\Exception(__('Payment tokenizer error.'));
        }
 
    
        $order = $payment->getOrder();
        $billing = $order->getBillingAddress();
        $payment->setIsTransactionClosed(0);
        $quote_Id = $order->getQuoteId();        
        $quote = $this->quoteFactory->create()
                                    ->load($quote_Id);
        
        $currency       = $order->getOrderCurrency(); //$order object

        if (is_object($currency)) {
            $logger->info("currencyjp:". $currency->getCurrencyCode()  ); 
        }    
        $logger->info("jose pecho");

        if( trim($this->registry->registry('sessionToken')) != ''){
           $sessionToken = trim($this->registry->registry('sessionToken'));
           $transactionToken = $this->registry->registry('transactionToken');
           $sessionKey = $this->registry->registry('sessionKey');        
        }
        else{
           
           $sessionKey = $payment->getAdditionalInformation('token'); // Este dato se envia x payment-information
           $transactionToken = $quote->getData('visanet_token');
        
        }
 

        $debug = $this->helperConfig->getConfig('payment/visanet_pay/visanetConfiguracion/debug');
        $ambiente = ($debug == '1') ? 'dev' : 'prd';

        $tokenType = $payment->getAdditionalInformation('token_type'); 

        // Esto es para las apps 
        if($tokenType == 'confirm_success'){

            $tarjeta = $payment->getAdditionalInformation('PAN');
            $id_unico = $payment->getAdditionalInformation('token');


            /*$fecha_pedido = $payment->getAdditionalInformation('FECHAYHORA_TX');
            $codaccion = $payment->getAdditionalInformation('CODACCION');
            $dsc_cod_accion = $payment->getAdditionalInformation('DSC_COD_ACCION');
            $autorizado = $payment->getAdditionalInformation('autorizado');*/

            $payment->setTransactionId($id_unico);


            //$payment->setAdditionalInformation('autorizado', $autorizado);
            //$payment->setShouldCloseParentTransaction(1);
            $payment->setTransactionAdditionalInfo(
                        'tarjeta',
                        $tarjeta
                    );
            $payment->setIsTransactionClosed(1);

        }
        else{

            unset($_SESSION['autorizado']);
            unset($_SESSION['tarjeta']);
            unset($_SESSION['fecha_pedido']);
            unset($_SESSION['DSC_COD_ACCION']);
            unset($_SESSION['CODACCION']); 
            unset($_SESSION['errorvisa']); 
            
            $currencyCode=$this->storeManager->getStore()->getBaseCurrencyCode();
            
            $merchant_id="";
            if($currencyCode=="USD")
            {
                $merchant_id=$this->helperConfig->getConfig('payment/visanet_pay/visanetConfiguracion/merchant_id_dollar');    
              
            }elseif($currencyCode=="PEN"){
                $merchant_id=$this->helperConfig->getConfig('payment/visanet_pay/visanetConfiguracion/merchant_id');
              
            }

          /*  $merchant_id = $this->helperConfig->getConfig('payment/visanet_pay/visanetConfiguracion/merchant_id');*/
            $access_key = $this->encryptor->decrypt($this->helperConfig->getConfig('payment/visanet_pay/visanetConfiguracion/public_key'));
            $SecretAccessKey = $this->encryptor->decrypt($this->helperConfig->getConfig('payment/visanet_pay/visanetConfiguracion/private_key'));
            $debug = $this->helperConfig->getConfig('payment/visanet_pay/visanetConfiguracion/debug');

            try {
 
                if(trim($transactionToken) == ''){
                    
                    throw new \Magento\Framework\Validator\Exception(__('Token de transacción no recibido'));
                }
                //$amount = round(($amount * 100),2) /100 ;
                $rawRespuestaVisa = $this->visanetManager->authorization($ambiente, $sessionKey, $amount,$transactionToken, $quote_Id,$merchant_id,$currencyCode);
                //var_dump('recibido: ', $rawRespuestaVisa);    
                $logger->info("rawanswer:". $rawRespuestaVisa  ); 
                $resultado =  json_decode($rawRespuestaVisa, true);            
                $statusCode=$resultado['statusCode'];                
               
               
                if( trim($statusCode) == 200){
                    //Variable para conocer si se uso los puntos
                    //true o false
                    $usoLosPuntos=false;
                    $esUnaCombinacionDepago=false;

                    if(isset($resultado['dataMap']['REDEEMED_EQUIVALENT_AMOUNT']))
                    {
                        $usoLosPuntos=true;                                

                        if($resultado['dataMap']['AMOUNT']!="0.0"){
                            $esUnaCombinacionDepago=true;
                        }

                    }





                    $codaccion = $resultado['dataMap']['ACTION_CODE']; // Código de denegación y aprobación. El Código de aprobación: 000.
                    //$autorizado = $resultado['dataMap']['RESPUESTA'];

                   $tarjeta="";
                    if($usoLosPuntos==false){
                        $tarjeta = $resultado['dataMap']['CARD'];                        
                    }
                    if($esUnaCombinacionDepago==true)
                    { 
                        $tarjeta = $resultado['dataMap']['CARD'];
                        
                    } 



                    $fecha_pedido = $resultado['dataMap']['TRANSACTION_DATE'];
                    $id_unico = $resultado['dataMap']['ID_UNICO'];  // ID único de la transacción del sistema Visanet

                    $dsc_cod_accion = $resultado['dataMap']['ACTION_DESCRIPTION']; // Descripción del código de acción, permite identificar el motivo de rechazo de una operación.
                    //$nrocuota = $resultado['dataMap']['NROCUOTA']; //Nro de cuota

                    //$_SESSION['autorizado'] = $autorizado;


                    $_SESSION['tarjeta'] = $tarjeta;

                    $_SESSION['fecha_pedido'] = $fecha_pedido;
                    $_SESSION['DSC_COD_ACCION'] = $dsc_cod_accion;
                    $_SESSION['CODACCION'] = $codaccion;
                  

                    $autorizado = 1;
                    $_SESSION['autorizado'] = $autorizado;
                    $payment->setTransactionId($id_unico);
                    $payment->setShouldCloseParentTransaction(1);
                    $payment->setTransactionAdditionalInfo(
                                'tarjeta',
                                $tarjeta
                            );
                    $payment->setAdditionalInformation('FECHAYHORA_TX', $fecha_pedido);
                    $payment->setAdditionalInformation('PAN', $tarjeta);
                    //$payment->setAdditionalInformation('NROCUOTA', $nrocuota);
                    $payment->setAdditionalInformation('CODACCION', $codaccion);
                    $payment->setAdditionalInformation('DSC_COD_ACCION', $dsc_cod_accion);
                    $payment->setAdditionalInformation('MONEDA', $currencyCode);
                    $payment->setAdditionalInformation('autorizado', $autorizado);

                    if($usoLosPuntos==true){
                        $redeemed_points=$resultado['dataMap']['REDEEMED_POINTS'];
                        $redeemed_equivalent_amount=$resultado['dataMap']['REDEEMED_EQUIVALENT_AMOUNT'];
                        $exchange_program_name=$resultado['dataMap']['EXCHANGE_PROGRAM_NAME'];
                        $exchange_id=$resultado['dataMap']['EXCHANGE_ID'];
                        $monto_tarjeta_combinada=$resultado['dataMap']['AMOUNT'];

                        $_SESSION['MONTOTARJETACOMBINADA'] = $monto_tarjeta_combinada;
                        $_SESSION['USOLOSPUNTOS'] = $usoLosPuntos;
                        $_SESSION['ESUNACOMBINACIONDEPAGO'] = $esUnaCombinacionDepago;
                        $_SESSION['REDEEMED_POINTS'] = $redeemed_points;
                        $_SESSION['REDEEMED_EQUIVALENT_AMOUNT'] = $redeemed_equivalent_amount;
                        $_SESSION['EXCHANGE_PROGRAM_NAME'] = $exchange_program_name;
                        $_SESSION['EXCHANGE_ID'] = $exchange_id;
                  

                    }



                    $payment->setIsTransactionClosed(1);

                }
                elseif( trim($statusCode) == 400)
                {
                    $autorizado = 1;

                    if(isset($resultado['data']['ACTION_CODE']))
                    {
                        $codaccion = $resultado['data']['ACTION_CODE']; // Código de denegación y aprobación. El Código de aprobación: 000.
                    }else{
                        $codaccion ="";
                    }
                    
                    if(isset($resultado['data']['ACTION_DESCRIPTION']))
                    {
                        $dsc_cod_accion = $resultado['data']['ACTION_DESCRIPTION']; // Descripción del código de acción, permite identificar el motivo de rechazo de una operación.    
                    }else{
                        $dsc_cod_accion = "";
                    }
                    
                    if(isset($resultado['errorMessage']))
                    {
                        $errorvisa = $resultado['errorMessage'];
                    }else{
                        $errorvisa = "";
                    }
                    
                    

                    $_SESSION['autorizado'] = $autorizado;     
                    $_SESSION['DSC_COD_ACCION'] = $dsc_cod_accion;
                    $_SESSION['CODACCION'] = $codaccion;
                    $_SESSION['errorvisa'] = $errorvisa;

                    $dsc_cod_accion = "Error Message:". $errorvisa.", Action Code: ".$codaccion.", Action Description:".$dsc_cod_accion ;
                    if($debug == '1'){
                        $dsc_cod_accion = $rawRespuestaVisa;
                    }

                    if($dsc_cod_accion == '') $dsc_cod_accion = 'No se pudo completar la operación';
  
                    throw new \Magento\Framework\Validator\Exception( __($dsc_cod_accion) );
                }
                else{                 
                     
  
                    throw new \Magento\Framework\Validator\Exception( __("Status Code: $statusCode, No se pudo conectar con visa API") );

                }
 

            } catch (\Exception $e) {

                $errorMessage = $e->getMessage();

                throw new \Magento\Framework\Validator\Exception(__($errorMessage));
      
            }

        }
 

        return $this;
    }

    /**
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float $amount
     * @return $this
     * @throws \Magento\Framework\Validator\Exception
     */
    public function refund(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
         
        return $this;
    }

    /**
     *
     * @param \Magento\Quote\Api\Data\CartInterface|null $quote
     * @return bool
     */
    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
  
  
        
        
        return parent::isAvailable($quote);
    }

    /**
     *
     * @param string $currencyCode
     * @return bool
     */
    public function canUseForCurrency($currencyCode)
    {
        if (!in_array($currencyCode, $this->_supportedCurrencyCodes)) {
            return false;
        }
        return true;
    }
}
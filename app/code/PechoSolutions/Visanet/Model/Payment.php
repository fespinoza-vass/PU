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

            $merchant_id = $this->helperConfig->getConfig('payment/visanet_pay/visanetConfiguracion/merchant_id');
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

                $resultado =  json_decode($rawRespuestaVisa, true);
                $statusCode=$resultado['statusCode'];


                if( trim($statusCode) == 200){
                    $codaccion = $resultado['dataMap']['ACTION_CODE']; // Código de denegación y aprobación. El Código de aprobación: 000.
                    //$autorizado = $resultado['dataMap']['RESPUESTA'];
                    $tarjeta = $resultado['dataMap']['CARD'];
                    $fecha_pedido = $resultado['dataMap']['TRANSACTION_DATE'];
                    $id_unico = $resultado['dataMap']['ID_UNICO'];  // ID único de la transacción del sistema Visanet

                    $authorization_code = $resultado['dataMap']['AUTHORIZATION_CODE'];
                    $brand = $resultado['dataMap']['BRAND'];
                    $brand_name = $resultado['dataMap']['BRAND_NAME'] ?? "";

                    $dsc_cod_accion = $resultado['dataMap']['ACTION_DESCRIPTION']; // Descripción del código de acción, permite identificar el motivo de rechazo de una operación.
                    //$nrocuota = $resultado['dataMap']['NROCUOTA']; //Nro de cuota

                    //$_SESSION['autorizado'] = $autorizado;
                    $_SESSION['tarjeta'] = $tarjeta;
                    $_SESSION['fecha_pedido'] = $fecha_pedido;
                    $_SESSION['DSC_COD_ACCION'] = $dsc_cod_accion;
                    $_SESSION['CODACCION'] = $codaccion;

                    $_SESSION['AUTHORIZATION_CODE'] = $authorization_code;
                    $_SESSION['BRAND'] = $brand;
                    $_SESSION['BRAND_NAME'] = $brand_name;


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

                    $payment->setAdditionalInformation('authorization_code', $authorization_code);
                    $payment->setAdditionalInformation('brand', $brand);
                    $payment->setAdditionalInformation('brand_name', $brand_name);

                    $payment->setIsTransactionClosed(1);

                }
                elseif( trim($statusCode) == 400)
                {
                    $autorizado = 1;
                    $codaccion = $resultado['data']['ACTION_CODE'] ?? ''; // Código de denegación y aprobación. El Código de aprobación: 000.
                    $dsc_cod_accion = $resultado['data']['ACTION_DESCRIPTION'] ?? ''; // Descripción del código de acción, permite identificar el motivo de rechazo de una operación.
                    $errorvisa = $resultado['errorMessage'] ?? '';

                    $_SESSION['autorizado'] = $autorizado;
                    $_SESSION['DSC_COD_ACCION'] = $dsc_cod_accion;
                    $_SESSION['CODACCION'] = $codaccion;
                    $_SESSION['errorvisa'] = $errorvisa;

                    $dsc_cod_accion = "Error Message:". $errorvisa.", Action Code: ".$codaccion.", Action Description:".$dsc_cod_accion;
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

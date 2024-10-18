<?php

namespace Izipay\Core\Helper;

use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Magento\Framework\App\Helper\AbstractHelper;
use \Izipay\Core\Model\IzipayFactory;
use \Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use \Izipay\Core\Logger\Logger;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{
	const CODE = 'izipay_core';

	const CONFIG_IZIPAY_ACTIVE = 'payment/izipay/active';

	const CONFIG_IZIPAY_GLOBALENV_ENVIRONMENT = 'payment/izipay/globalenv/environment';
	const CONFIG_IZIPAY_GLOBALENV_TYPEACCESS = 'payment/izipay/globalenv/type_access';
	
	// Configuraciones por entorno

	const CONFIG_IZIPAY_CONFIGDEV_SOLES_MERCHANTCODE = 'payment/izipay/configdev/soles/merchant_code';
	const CONFIG_IZIPAY_CONFIGDEV_SOLES_PUBLICKEY = 'payment/izipay/configdev/soles/public_key';
	const CONFIG_IZIPAY_CONFIGDEV_DOLAR_MERCHANTCODE = 'payment/izipay/configdev/dolar/merchant_code';
	const CONFIG_IZIPAY_CONFIGDEV_DOLAR_PUBLICKEY = 'payment/izipay/configdev/dolar/public_key';
	const CONFIG_IZIPAY_CONFIGDEV_NOTIFICATIONURL = 'payment/izipay/configdev/notification_url';
	
	const CONFIG_IZIPAY_CONFIGPRO_SOLES_MERCHANTCODE = 'payment/izipay/configpro/soles/merchant_code';
	const CONFIG_IZIPAY_CONFIGPRO_SOLES_PUBLICKEY = 'payment/izipay/configpro/soles/public_key';
	const CONFIG_IZIPAY_CONFIGPRO_DOLAR_MERCHANTCODE = 'payment/izipay/configpro/dolar/merchant_code';
	const CONFIG_IZIPAY_CONFIGPRO_DOLAR_PUBLICKEY = 'payment/izipay/configpro/dolar/public_key';
	const CONFIG_IZIPAY_CONFIGPRO_NOTIFICATIONURL = 'payment/izipay/configpro/notification_url';

	// Metodos de pago

	const CONFIG_IZIPAY_PAYMENTOPTIONS_METHOD1_ACTIVE = 'payment/izipay/paymentoptions/method1/paymentactive';
	const CONFIG_IZIPAY_PAYMENTOPTIONS_METHOD1_PAYMENT = 'payment/izipay/paymentoptions/method1/paymentmethod';
	const CONFIG_IZIPAY_PAYMENTOPTIONS_METHOD1_TITLE = 'payment/izipay/paymentoptions/method1/paymenttitle';
	const CONFIG_IZIPAY_PAYMENTOPTIONS_METHOD1_DESCRIPTION = 'payment/izipay/paymentoptions/method1/paymentdescription';

	const CONFIG_IZIPAY_PAYMENTOPTIONS_METHOD2_ACTIVE = 'payment/izipay/paymentoptions/method2/paymentactive';
	const CONFIG_IZIPAY_PAYMENTOPTIONS_METHOD2_PAYMENT = 'payment/izipay/paymentoptions/method2/paymentmethod';
	const CONFIG_IZIPAY_PAYMENTOPTIONS_METHOD2_TITLE = 'payment/izipay/paymentoptions/method2/paymenttitle';
	const CONFIG_IZIPAY_PAYMENTOPTIONS_METHOD2_DESCRIPTION = 'payment/izipay/paymentoptions/method2/paymentdescription';

	const CONFIG_IZIPAY_PAYMENTOPTIONS_METHOD3_ACTIVE = 'payment/izipay/paymentoptions/method3/paymentactive';
	const CONFIG_IZIPAY_PAYMENTOPTIONS_METHOD3_PAYMENT = 'payment/izipay/paymentoptions/method3/paymentmethod';
	const CONFIG_IZIPAY_PAYMENTOPTIONS_METHOD3_TITLE = 'payment/izipay/paymentoptions/method3/paymenttitle';
	const CONFIG_IZIPAY_PAYMENTOPTIONS_METHOD3_DESCRIPTION = 'payment/izipay/paymentoptions/method3/paymentdescription';

	const CONFIG_IZIPAY_PAYMENTOPTIONS_METHOD4_ACTIVE = 'payment/izipay/paymentoptions/method4/paymentactive';
	const CONFIG_IZIPAY_PAYMENTOPTIONS_METHOD4_PAYMENT = 'payment/izipay/paymentoptions/method4/paymentmethod';
	const CONFIG_IZIPAY_PAYMENTOPTIONS_METHOD4_TITLE = 'payment/izipay/paymentoptions/method4/paymenttitle';
	const CONFIG_IZIPAY_PAYMENTOPTIONS_METHOD4_DESCRIPTION = 'payment/izipay/paymentoptions/method4/paymentdescription';

	const CONFIG_IZIPAY_PAYMENTOPTIONS_METHOD5_ACTIVE = 'payment/izipay/paymentoptions/method5/paymentactive';
	const CONFIG_IZIPAY_PAYMENTOPTIONS_METHOD5_PAYMENT = 'payment/izipay/paymentoptions/method5/paymentmethod';
	const CONFIG_IZIPAY_PAYMENTOPTIONS_METHOD5_TITLE = 'payment/izipay/paymentoptions/method5/paymenttitle';
	const CONFIG_IZIPAY_PAYMENTOPTIONS_METHOD5_DESCRIPTION = 'payment/izipay/paymentoptions/method5/paymentdescription';

	// Apariencia 

	const CONFIG_IZIPAY_APPEARANCE_LOGO = 'payment/izipay/appearance/logo';
	const CONFIG_IZIPAY_APPEARANCE_TYPEFORM = 'payment/izipay/appearance/typeform';
	const CONFIG_IZIPAY_APPEARANCE_STYLEINPUT = 'payment/izipay/appearance/styleinput';
	const CONFIG_IZIPAY_APPEARANCE_THEME = 'payment/izipay/appearance/theme';

	// Terminos y condiciones
	const CONFIG_IZIPAY_TERMSANDCONDITIONS_URL = 'payment/izipay/termsandconditions/url';

	const CONFIG_IZIPAY_PROCESSING_STATUS = 'payment/izipay/processing_izipay';
	const CONFIG_IZIPAY_PENDING_PAYMENT_STATUS = 'payment/izipay/pending_payment_izipay';
	const CONFIG_IZIPAY_CANCELED_STATUS = 'payment/izipay/canceled_izipay';

	// URLs para generación de token
	//const URL_TOKEN_DEV = 'https://testapi-pw.izipay.pe/security/v1/Token/Generate';
	const URL_TOKEN_DEV = 'https://sandbox-api-pw.izipay.pe/security/v1/Token/Generate';
	const URL_TOKEN_PROD = 'https://api-pw.izipay.pe/security/v1/Token/Generate';

	//const URL_SDK_DEV = 'https://testcheckout.izipay.pe/payments/v1/js/index.js';
	const URL_SDK_DEV = 'https://sandbox-checkout.izipay.pe/payments/v1/js/index.js';
	const URL_SDK_PROD = 'https://checkout.izipay.pe/payments/v1/js/index.js';

	const PRODUCTION_ENVIRONMENT = 'production';

	protected $_logger;
	protected $_izipayFactory;
	protected $_date;
	protected $_storeManager;

	public function __construct(
		IzipayFactory $_izipayFactory,
		PricingHelper $_pricingHelper,
		Logger $_logger,
		Context $context,
		DateTime $_date,
		StoreManagerInterface $storeManager
	)
	{
	    $this->_izipayFactory = $_izipayFactory;
	    $this->_pricingHelper = $_pricingHelper;
	    $this->_logger = $_logger;
	    $this->_date = $_date;
		$this->_storeManager = $storeManager;
	    parent::__construct($context);
	}

	public function getPaymentStatuses($statusCode){
		$statuses = [
			"00" => "Operación exitosa",
			"01" => "Consulte al emisor de la tarjeta.",
			"03" => "Comerciante no válido (Tarjeta no afiliada).",
			"04" => "Retener tarjeta.",
			"05" => "Denegado.",
			"12" => "Transacción no válida.",
			"14" => "Número de tarjeta inválido",
			"15" => "Emisor inválido.",
			"41" => "Tarjeta perdida.",
			"43" => "Tarjeta robada.",
			"54" => "Tarjeta expirada.",
			"57" => "Transacción no permitida al emisor / titular de la tarjeta.",
			"58" => "Transacción no permitida al adquirente / terminal.",
			"62" => "Tarjeta restringida.",
			"63" => "Violación de Seguridad.",
			"94" => "Transmisión duplicada detectada.",
			"N7" => "CVV2 erróneo (válido sólo para VISA).",
			"13" => "Monto inválido.",
			"51" => "Fondos insuficientes / Límite de crédito excedido.",
			"61" => "Excede el límite de monto de retiro.",
			"91" => "Sistema de autorización o sistema emisor inoperante.",
			"96" => "Error del sistema.",
			"S01" => "Error de validación (metodo de pago, marca, fechaExp, nro tarjeta, monto, moneda, nroOrden, comercio) que seas nulos o vacios.",
			"S02" => "Error de BD (mala configuración).",
			"S03" => "Error de Lógica del API Scoring.",
			"S04" => "Error Conexión (Api Cybersource).",
			"S05" => "Error API Cybersource.",
			"S09" => "Error de Sistema.",
			"A03" => "Falló la autenticación del Tarjetahabiente.",
			"A04" => "La autenticación no ha podido culminar por problemas técnicos.",
			"A05" => "Problemas con el Directory Server.",
			"A06" => "Error en Método de Verificación de Tarjetas Enrroladas.",
			"A07" => "Tarjeta No Enrrolada.",
			"A08" => "La autenticación 3D Secure NO fue Exitosa.",
			"T01" => "No se envió datos en la solicitud.",
			"T02" => "Se envió incorrecto el campo.",
			"T03" => "Ocurrió un error interno"
		];

		return @$statuses[$statusCode] ? $statuses[$statusCode] : "[".$statusCode."]".'Ha ocurrido un error.';
	}

	public function formatPaymentAmount($amount){
		return number_format($amount,2);
	}

	public function getActive(){
		return $this->getConfig($this::CONFIG_IZIPAY_ACTIVE);
	}

	public function getEnvironment(){
		return $this->getConfig($this::CONFIG_IZIPAY_GLOBALENV_ENVIRONMENT);
	}

	public function getTypeAccess(){
		return $this->getConfig($this::CONFIG_IZIPAY_GLOBALENV_TYPEACCESS);
	}

	public function getProcessingStatus(){
		return $this->getConfig($this::CONFIG_IZIPAY_PROCESSING_STATUS);
	}
	public function getPendingPaymentStatus(){
		return $this->getConfig($this::CONFIG_IZIPAY_PENDING_PAYMENT_STATUS);
	}
	public function getCanceledStatus(){
		return $this->getConfig($this::CONFIG_IZIPAY_CANCELED_STATUS);
	}

	public function getMerchantCode(){
		$currency = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();

		if ($this->getEnvironment() == $this::PRODUCTION_ENVIRONMENT) {
			if ($currency == "PEN") {
				return $this->getConfig($this::CONFIG_IZIPAY_CONFIGPRO_SOLES_MERCHANTCODE);
			} else {
				return $this->getConfig($this::CONFIG_IZIPAY_CONFIGPRO_DOLAR_MERCHANTCODE);
			}
		} else {
			if ($currency == "PEN") {
				return $this->getConfig($this::CONFIG_IZIPAY_CONFIGDEV_SOLES_MERCHANTCODE);
			} else {
				return $this->getConfig($this::CONFIG_IZIPAY_CONFIGDEV_DOLAR_MERCHANTCODE);
			}
		}
	}

	public function getPublicKey(){
		$currency = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();

		if ($this->getEnvironment() == $this::PRODUCTION_ENVIRONMENT) {
			if ($currency == "PEN") {
				return $this->getConfig($this::CONFIG_IZIPAY_CONFIGPRO_SOLES_PUBLICKEY);
			} else {
				return $this->getConfig($this::CONFIG_IZIPAY_CONFIGPRO_DOLAR_PUBLICKEY);
			}
		} else {
			if ($currency == "PEN") {
				return $this->getConfig($this::CONFIG_IZIPAY_CONFIGDEV_SOLES_PUBLICKEY);
			} else {
				return $this->getConfig($this::CONFIG_IZIPAY_CONFIGDEV_DOLAR_PUBLICKEY);
			}
		}
	}

	public function getNotificationUrl(){
		if ($this->getEnvironment() == $this::PRODUCTION_ENVIRONMENT) {
			return $this->getConfig($this::CONFIG_IZIPAY_CONFIGPRO_NOTIFICATIONURL);
		} else {
			return $this->getConfig($this::CONFIG_IZIPAY_CONFIGDEV_NOTIFICATIONURL);
		}
	}

	public function getAppearanceLogo(){
		return $this->getConfig($this::CONFIG_IZIPAY_APPEARANCE_LOGO);
	}

	public function getAppearanceTypeForm(){
		return $this->getConfig($this::CONFIG_IZIPAY_APPEARANCE_TYPEFORM);
	}

	public function getAppearanceStyleInput(){
		return $this->getConfig($this::CONFIG_IZIPAY_APPEARANCE_STYLEINPUT);
	}

	public function getAppearanceTheme(){
		return $this->getConfig($this::CONFIG_IZIPAY_APPEARANCE_THEME);
	}

	public function getTermsAndConditions(){
		return $this->getConfig($this::CONFIG_IZIPAY_TERMSANDCONDITIONS_URL);
	}

	public function getAlternativePaymentMethods() {

		$is_active_payment_method1 = $this->getConfig($this::CONFIG_IZIPAY_PAYMENTOPTIONS_METHOD1_ACTIVE);
		$is_active_payment_method2 = $this->getConfig($this::CONFIG_IZIPAY_PAYMENTOPTIONS_METHOD2_ACTIVE);
		$is_active_payment_method3 = $this->getConfig($this::CONFIG_IZIPAY_PAYMENTOPTIONS_METHOD3_ACTIVE);
		$is_active_payment_method4 = $this->getConfig($this::CONFIG_IZIPAY_PAYMENTOPTIONS_METHOD4_ACTIVE);
		$is_active_payment_method5 = $this->getConfig($this::CONFIG_IZIPAY_PAYMENTOPTIONS_METHOD5_ACTIVE);

		$alternative_payment_methods = [];

		// verificamos si alguno de esos métodos de pago está activo
		if ($is_active_payment_method1 || 
			$is_active_payment_method2 || 
			$is_active_payment_method3 || 
			$is_active_payment_method4 || 
			$is_active_payment_method5) {

				if ($is_active_payment_method1) {
					$method = [];
					$method["type"] = "method1";
					$method["title"] = $this->getConfig($this::CONFIG_IZIPAY_PAYMENTOPTIONS_METHOD1_TITLE);
					$method["description"] = $this->getConfig($this::CONFIG_IZIPAY_PAYMENTOPTIONS_METHOD1_DESCRIPTION);
					$method["payment_method"] = $this->getConfig($this::CONFIG_IZIPAY_PAYMENTOPTIONS_METHOD1_PAYMENT);
					array_push($alternative_payment_methods, $method);

				} 
				
				if ($is_active_payment_method2) { 
					$method = [];
					$method["type"] = "method2";
					$method["title"] = $this->getConfig($this::CONFIG_IZIPAY_PAYMENTOPTIONS_METHOD2_TITLE);
					$method["description"] = $this->getConfig($this::CONFIG_IZIPAY_PAYMENTOPTIONS_METHOD2_DESCRIPTION);
					$method["payment_method"] = $this->getConfig($this::CONFIG_IZIPAY_PAYMENTOPTIONS_METHOD2_PAYMENT);
					array_push($alternative_payment_methods, $method);

				} 
				
				if ($is_active_payment_method3) { 
					$method = [];
					$method["type"] = "method3";
					$method["title"] = $this->getConfig($this::CONFIG_IZIPAY_PAYMENTOPTIONS_METHOD3_TITLE);
					$method["description"] = $this->getConfig($this::CONFIG_IZIPAY_PAYMENTOPTIONS_METHOD3_DESCRIPTION);
					$method["payment_method"] = $this->getConfig($this::CONFIG_IZIPAY_PAYMENTOPTIONS_METHOD3_PAYMENT);
					array_push($alternative_payment_methods, $method);

				} 
				
				if ($is_active_payment_method4) { 
					$method = [];
					$method["type"] = "method4";
					$method["title"] = $this->getConfig($this::CONFIG_IZIPAY_PAYMENTOPTIONS_METHOD4_TITLE);
					$method["description"] = $this->getConfig($this::CONFIG_IZIPAY_PAYMENTOPTIONS_METHOD4_DESCRIPTION);
					$method["payment_method"] = $this->getConfig($this::CONFIG_IZIPAY_PAYMENTOPTIONS_METHOD4_PAYMENT);
					array_push($alternative_payment_methods, $method);

				} 
				
				if ($is_active_payment_method5) { 
					$method = [];
					$method["type"] = "method5";
					$method["title"] = $this->getConfig($this::CONFIG_IZIPAY_PAYMENTOPTIONS_METHOD5_TITLE);
					$method["description"] = $this->getConfig($this::CONFIG_IZIPAY_PAYMENTOPTIONS_METHOD5_DESCRIPTION);
					$method["payment_method"] = $this->getConfig($this::CONFIG_IZIPAY_PAYMENTOPTIONS_METHOD5_PAYMENT);
					array_push($alternative_payment_methods, $method);

				}
		}	

		return $alternative_payment_methods;
	}

	public function getUrlSdk() {
		if ($this->getEnvironment() == $this::PRODUCTION_ENVIRONMENT) {
			return $this::URL_SDK_PROD;
		} else {
			return $this::URL_SDK_DEV;
		}
	}

	public function getConfig($path) {
        return $this->scopeConfig->getValue($path,ScopeInterface::SCOPE_STORE);
    }

    public function getToken($requestToken, $transaction_id) {
		$merchant_code = $this->getMerchantCode();
		$public_key = $this->getPublicKey();
		

		$requestToken["merchantCode"] = $merchant_code;
		$requestToken["publicKey"] = $public_key;

		//$this->_logger->debug($requestToken["merchantCode"]);
		//$this->_logger->debug($requestToken["publicKey"]);

		$ch = curl_init();

		$url_token = $this::URL_TOKEN_DEV;
		if($this->getEnvironment() == $this::PRODUCTION_ENVIRONMENT) {
			$url_token = $this::URL_TOKEN_PROD;
		}

		curl_setopt($ch, CURLOPT_URL, $url_token);
		curl_setopt($ch, CURLOPT_POST, 1);
		
		$requestToken_string = json_encode($requestToken);

		$headers = array();
		$headers[] = 'Content-Type: application/json';
		$headers[] = 'transactionId:'.$transaction_id;
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $requestToken_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$result = curl_exec($ch);

		$response = null;
        if ($result === false) {
            $this->_logger->error("Curl error", array("curl_errno" => curl_errno($ch), "curl_error" => curl_error($ch)));
			return null;
        } else {
            $info = curl_getinfo($ch);
            $response = json_decode($result);
            $response->http_code = $info['http_code'];
            $this->_logger->debug("REQUEST IZIPAY: ", array("HTTP code " => $info['http_code'], "on request to" => $info['url']));
        }

		curl_close($ch);
		$this->_logger->debug('REQUEST IZIPAY response: ', [json_encode($response)]);
    	
		return $response;
    }

}

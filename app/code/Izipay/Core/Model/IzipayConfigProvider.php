<?php

namespace Izipay\Core\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Izipay\Core\Helper\Data as IzipayHelper;
use Magento\Checkout\Model\Cart;
use \Izipay\Core\Logger\Logger;

class IzipayConfigProvider implements ConfigProviderInterface
{
    
    protected $_logger;
    protected $cart;
    protected $izipayHelper;
    protected $payment ;

    public function __construct(
        Logger $_logger,
        IzipayHelper $izipayHelper,
        Cart $cart) {
        
        $this->_logger = $_logger;
        $this->cart = $cart;
        $this->izipayHelper = $izipayHelper;
    }

    public function getConfig()
    {
        $config = [];
        
        $config['payment']['izipay']['alternative_payment_methods'] = $this->izipayHelper->getAlternativePaymentMethods();
        $config['payment']['izipay']['appearence']['theme'] = $this->izipayHelper->getAppearanceTheme();
        $config['payment']['izipay']['appearence']['style_input'] = $this->izipayHelper->getAppearanceStyleInput();
        $config['payment']['izipay']['appearence']['type_form'] = $this->izipayHelper->getAppearanceTypeForm();
        $config['payment']['izipay']['appearence']['logo'] = $this->izipayHelper->getAppearanceLogo();
        $config['payment']['izipay']['notification_url'] = $this->izipayHelper->getNotificationUrl();
        $config['payment']['izipay']['type_access'] = $this->izipayHelper->getTypeAccess();

        $config['payment']['izipay']['public_key'] = $this->izipayHelper->getPublicKey();
        $config['payment']['izipay']['merchant_code'] = $this->izipayHelper->getMerchantCode();

        return $config;
    }

}

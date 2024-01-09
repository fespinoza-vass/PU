<?php


namespace Niubiz\Ncp\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Niubiz\Ncp\Model\Library\Ncp;
use Magento\Store\Model\StoreManagerInterface;

class AdditionalConfigProvider implements ConfigProviderInterface
{
    /**
     * @var \Magento\Payment\Gateway\Config\Config
     */
    private $config;
    protected $checkoutSession;
    protected $logger;
    protected $cart;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    private $encryptor;
    private $assetRepository;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * Initialize dependencies.
     *
     * @param \Magento\Payment\Gateway\Config\Config $config
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     */
    public function __construct(
        \Magento\Payment\Gateway\Config\Config $config,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\View\Asset\Repository $assetRepository,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Store\Model\StoreManagerInterface $storeManager


    )
    {
        $this->config = $config;
        $this->encryptor = $encryptor;
        $this->checkoutSession = $checkoutSession;
        $this->assetRepository = $assetRepository;
        $this->logger = $logger;
        $this->cart=$cart;
        $this->storeManager = $storeManager;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/visanew.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);


        $media_dir = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $grandTotal = $this->cart->getQuote()->getGrandTotal();
        $debug = $this->config->getValue('NcpConfiguracion/debug');

        if ($debug == '1'){
            $ambiente = 'dev';
        }else{
            $ambiente = 'prd';
        }

        $quoteId = $this->checkoutSession->getQuoteId();
        $quote = $this->checkoutSession->getQuote();
        $tokenTrassactionExist = '0';
        $transactionToken = $quote->getData('Ncp_token');
        $currencyCode=$this->storeManager->getStore()->getBaseCurrencyCode();
        $merchant_id="";
        if($currencyCode=="USD")
        {
            $merchant_id=$this->config->getValue('NcpConfiguracion/merchant_id_dollar');
        }elseif($currencyCode=="PEN"){
            $merchant_id=$this->config->getValue('NcpConfiguracion/merchant_id');
        }
        $logger->info("Merchant Id jp2");
        $logger->info($merchant_id);
        /**
         *  'merchantId' => $this->config->getValue('NcpConfiguracion/merchant_id'),**/

        if($transactionToken != ''){
            $tokenTrassactionExist = '1';
        }

        $logo_visa  =  $this->assetRepository->getUrl('Niubiz_Ncp::images/Ncp_pay.png');

        return [
            'payment' => [
                Payment::CODE => [
                    'isActive' => $this->config->getValue('active'),
                    'tokenTrassactionExist' => $tokenTrassactionExist,
                    'title' => $this->config->getValue('title'),
                    'publicKey' => $this->encryptor->decrypt($this->config->getValue('NcpConfiguracion/public_key')),
                    'privateKey' => $this->encryptor->decrypt($this->config->getValue('NcpConfiguracion/private_key')),
                    'actionUrl' => $this->storeManager->getStore()->getBaseUrl() . 'Ncp/visa/web',
                    'merchantId' => $merchant_id,
                    'vex_formbuttoncolor' => $this->config->getValue('NcpConfiguracion/formbuttoncolor'),
                    'vex_showamount' => $this->config->getValue('NcpConfiguracion/showamount'),
                    'vex_buttonsize' => $this->config->getValue('NcpConfiguracion/buttonsize'),
                    'upload_image' => $media_dir."image/".$this->config->getValue('NcpConfiguracion/upload_image'),
                    'quoteId' => ( ($debug == '1') ? ($quoteId+18002) : $quoteId),
                    'quote_id' => $quoteId,
                    'storeTitle' => $this->config->getValue('NcpConfiguracion/store_title'),
                    'description' => $this->config->getValue('NcpConfiguracion/store_desc'),
                    'currency' => $this->storeManager->getStore()->getCurrentCurrency()->getCode(),
                    'amount' => $grandTotal,
                    'subtotal' => $this->cart->getQuote()->getSubtotal(),
                    'subtotalWithDiscount' =>  $this->cart->getQuote()->getSubtotalWithDiscount(),
                    'logo_visa' => $logo_visa,
                    'terminos' => $this->config->getValue('NcpConfiguracion/terminos_condiciones'),
                    'base_url' => $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK)
                ]
            ]
        ];
    }
}

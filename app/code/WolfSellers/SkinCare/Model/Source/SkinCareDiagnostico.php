<?php

namespace WolfSellers\SkinCare\Model\Source;

use Magento\Customer\Model\Session;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Pricing\Helper\Data as DataPricing;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Store\Model\ScopeInterface;

class SkinCareDiagnostico
{

    protected Session $_customerSession;
    protected ProductRepositoryInterface $_productRepository;
    protected DataPricing $_priceHelper;

    protected $transportBuilder;
    protected $inlineTranslation;
    protected $escaper;
    protected $logger;

    public $_diagnosticoResult;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param DataPricing $priceHelper
     * @param StoreManagerInterface $storeManager
     * @param StateInterface $inlineTranslation
     * @param Escaper $escaper
     * @param TransportBuilder $transportBuilder
     * @param Session $customerSession
     * @param ScopeConfigInterface $scopeConfig
     */

    public function __construct(
        ProductRepositoryInterface                 $productRepository,
        DataPricing                                $priceHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        StateInterface                             $inlineTranslation,
        Escaper                                    $escaper,
        TransportBuilder                           $transportBuilder,
        Session                                    $customerSession,
        ScopeConfigInterface                       $scopeConfig
    )
    {
        $this->_customerSession = $customerSession;
        $this->_productRepository = $productRepository;
        $this->_storeManager = $storeManager;
        $this->_priceHelper = $priceHelper;
        $this->_diagnosticoResult = [];
        $this->inlineTranslation = $inlineTranslation;
        $this->escaper = $escaper;
        $this->transportBuilder = $transportBuilder;
        $this->_scopeConfig = $scopeConfig;
    }


    /**
     * @return void
     */
    public function sendEmail($email)
    {
        try {
            $diagnostico = new \Magento\Framework\DataObject();
            $diagnostico->setData($this->_diagnosticoResult);
            $this->inlineTranslation->suspend();
            $emailStore = $this->_scopeConfig->getValue('trans_email/ident_support/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $name  = $this->_scopeConfig->getValue('trans_email/ident_support/name',ScopeInterface::SCOPE_STORE);
            $sender = [
                'name' => $this->escaper->escapeHtml($name),
                'email' => $this->escaper->escapeHtml($emailStore),
            ];
            $transport = $this->transportBuilder
                ->setTemplateIdentifier('skin_care_diagnostico')
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars([
                    'diagnostico'  => $diagnostico,
                ])
                ->setFrom($sender)
                ->addTo($email)
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
        }
    }




}

<?php

namespace WolfSellers\SkinCare\Model\Source;


use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

use Magento\Store\Model\ScopeInterface;

class SkinCareDiagnostico
{
    protected $transportBuilder;
    protected $inlineTranslation;
    protected $escaper;
    protected $logger;


    /**
     * @param StoreManagerInterface $storeManager
     * @param StateInterface $inlineTranslation
     * @param Escaper $escaper
     * @param TransportBuilder $transportBuilder
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        StateInterface        $inlineTranslation,
        Escaper               $escaper,
        TransportBuilder      $transportBuilder,
        ScopeConfigInterface  $scopeConfig
    )
    {
        $this->_storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->escaper = $escaper;
        $this->transportBuilder = $transportBuilder;
        $this->_scopeConfig = $scopeConfig;
    }


    /**
     * Receives the email and data, prepares the template and send the email
     * @param string $email
     * @param array $diagnosticoArray
     * @return void
     */
    public function sendEmail(string $email, array $diagnosticoArray)
    {
        try {
            $diagnostico = new \Magento\Framework\DataObject();
            $diagnostico->setData($diagnosticoArray);
            $this->inlineTranslation->suspend();
            $emailStore = $this->_scopeConfig->getValue('trans_email/ident_support/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $name = $this->_scopeConfig->getValue('trans_email/ident_support/name', ScopeInterface::SCOPE_STORE);
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
                    'diagnostico' => $diagnostico,
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

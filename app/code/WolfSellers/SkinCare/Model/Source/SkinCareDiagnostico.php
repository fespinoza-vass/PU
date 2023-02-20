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
     * @param $productCollection
     * @param $type
     * @param $value
     * @return array
     */
    public function setProductCollection($productCollection, $type, $value, $email)
    {
        $items = $productCollection->getData();
        $store = $this->_storeManager->getStore();

        $result = [];
        $result['dark_circle'] = [];
        $result['wrinkle'] = [];
        $result['texture'] = [];
        $result['spot'] = [];

        $result['results']['dark_circle'] = 0;
        $result['results']['texture'] = 0;
        $result['results']['wrinkle'] = 0;
        $result['results']['spot'] = 0;

        foreach ($items as $item):
            $product = $this->_productRepository->getById($item['entity_id']);
            $productInfo['urlImage'] = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $product->getImage();
            $productInfo['price'] = $this->_priceHelper->currency($product->getPrice(), true, false);
            $productInfo['name'] = $product->getName();
            $productInfo['urlProduct'] = $product->getProductUrl();

            if ($type == 'dark_circle' && count($result['dark_circle']) < 4):
                $result['dark_circle'][] = $productInfo;
                $result['results']['dark_circle'] = $value;

            elseif($type == 'texture' && count($result['texture']) < 4):
                $result['texture'][] = $productInfo;
                $result['results']['texture'] = $value;

            elseif($type == 'wrinkle' && count($result['wrinkle']) < 4):
                $result['wrinkle'][] = $productInfo;
                $result['results']['wrinkle'] = $value;

            elseif($type == 'spot' && count($result['spot']) < 4):
                $result['spot'][] = $productInfo;
                $result['results']['spot'] = $value;
            endif;

        endforeach;

        $this->_diagnosticoResult = $result;
        $this->sendEmail($$email);

        return $result;
    }


    public function getProductCollection()
    {
        return $this->_diagnosticoResult;
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

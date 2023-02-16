<?php

namespace WolfSellers\SkinCare\Model\Source;

use Magento\Customer\Model\Session;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Pricing\Helper\Data as DataPricing;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;

class SkinCareDiagnostico
{

    protected Session $_customerSession;
    protected ProductRepositoryInterface $_productRepository;
    protected DataPricing $_priceHelper;
    protected $transportBuilder;
    protected $storeManager;
    protected $inlineTranslation;
    public $_productListIds;

    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        ProductRepositoryInterface $productRepository,
        DataPricing $priceHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder,
        #StoreManagerInterface $storeManager,
        StateInterface $state,
        Session $customerSession
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_customerSession = $customerSession;
        $this->_productRepository = $productRepository;
        $this->_storeManager = $storeManager;
        $this->_priceHelper = $priceHelper;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $state;
        $this->_productListIds = [];
    }


    /**
     * @param $productCollection
     * @param $type
     * @param $value
     * @return array
     */
    public function setProductCollection($productCollection, $type, $value)
    {
        $items= $productCollection->getData();
        $store = $this->_storeManager->getStore();

        $result= [];
        $result['dark_circle']= [];
        $result['wrinkle']= [];
        $result['texture']= [];
        $result['spot']= [];

        $result['results']['dark_circle'] = 0;
        $result['results']['texture'] = 0;
        $result['results']['wrinkle'] = 0;
        $result['results']['spot'] = 0;

        if($type == 'dark_circle'):
            $result['results']['dark_circle'] = $value;
        endif;
        if($type == 'texture'):
            $result['results']['texture'] = $value;
        endif;
        if($type == 'wrinkle'):
            $result['results']['wrinkle'] = $value;
        endif;
        if($type == 'spot'):
            $result['results']['spot'] = $value;
        endif;

        foreach ($items as $item):
            $product = $this->_productRepository ->getById($item['entity_id']);
            $productInfo['urlImage'] = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $product->getImage();
            $productInfo['price'] = $this->_priceHelper->currency($product->getPrice(),true,false);
            $productInfo['name']= $product->getName();

            $productInfo['urlProduct']= $product->getProductUrl();
            if($type == 'dark_circle' && count($result['dark_circle']) < 4):
                array_push($result['dark_circle'], $product['entity_id']);
            endif;
            if($type == 'texture' && count($result['texture']) < 4):
                array_push($result['texture'], $product['entity_id']);
            endif;
            if($type == 'wrinkle' && count($result['wrinkle']) < 4):
                array_push($result['wrinkle'], $product['entity_id']);
            endif;
            if($type == 'spot' && count($result['spot']) < 4):
                array_push($result['spot'], $product['entity_id']);
            endif;
            if($type == 'dark_circle' && count($result['dark_circle']) < 4):
                array_push($result['dark_circle'], $productInfo);
            endif;
            if($type == 'texture' && count($result['texture']) < 4):
                array_push($result['texture'], $productInfo);
            endif;
            if($type == 'wrinkle' && count($result['wrinkle']) < 4):
                array_push($result['wrinkle'], $productInfo);
            endif;
            if($type == 'spot' && count($result['spot']) < 4):
                array_push($result['spot'], $productInfo);
            endif;
        endforeach;

/*
        foreach($items as $item):

            $product = $this->_productRepository ->getById($item['entity_id']);
            $productInfo['urlImage'] = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $product->getImage();
            $productInfo['price'] = $this->_priceHelper->currency($product->getPrice(),true,false);
            $productInfo['name']= $product->getName();
            $productInfo['urlProduct']= $product->getProductUrl();

            if($type == 'dark_circle' && count($result['dark_circle']) < 4):
                array_push($result['dark_circle'], $productInfo);
            endif;
            if($type == 'texture' && count($result['texture']) < 4):
                array_push($result['texture'], $productInfo);
            endif;
            if($type == 'wrinkle' && count($result['wrinkle']) < 4):
                array_push($result['wrinkle'], $productInfo);
            endif;
            if($type == 'spot' && count($result['spot']) < 4):
                array_push($result['spot'], $productInfo);
            endif;

        endforeach;

*/
        $this->_customerSession->setDiagnostico($result);
        $this->_coreRegistry->register('diagnostico', $result);
        $this->_productListIds = $result;

        return $result;
    }

    public function getProductCollection()
    {
        #return $this->_coreRegistry->registry('diagnostico');
        return $this->_productListIds;#$this->_customerSession->getDiagnostico();
    }

    public function sendEmail()
    {
        $templateId = 'my_custom_email_template'; // template id
        $fromEmail = 'owner@domain.com';  // sender Email id
        $fromName = 'Admin';             // sender Name
        $toEmail = 'customer@email.com'; // receiver email id

        try {
            // template variables pass here
            $templateVars = [
                'msg' => 'test',
                'msg1' => 'test1'
            ];

            $storeId = $this->_storeManager->getStore()->getId();

            $from = ['email' => $fromEmail, 'name' => $fromName];
            $this->inlineTranslation->suspend();

            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $templateOptions = [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $storeId
            ];
            $transport = $this->transportBuilder->setTemplateIdentifier($templateId, $storeScope)
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFrom($from)
                ->addTo($toEmail)
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->_logger->info($e->getMessage());
        }
    }








}

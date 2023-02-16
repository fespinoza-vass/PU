<?php

namespace WolfSellers\SkinCare\Block\Email;

use Magento\Framework\View\Element\Template;
use \Magento\Framework\View\Element\Template\Context;
use WolfSellers\SkinCare\Model\Source\SkinCareDiagnostico;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Pricing\Helper\Data as DataPricing;
use Magento\Customer\Model\Session;



class Diagnostico extends Template
{
    public SkinCareDiagnostico $_skinCare;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var Json
     */
    protected $serializer;
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var Http
     */
    protected $http;
    private LayoutFactory $layoutFactory;
    private ProductCollectionFactory $productCollectionFactory;
    private RequestInterface $request;
    private ResourceConnection $resourceConnection;

    protected SkinCareDiagnostico $_skinCareDiagnostico;
    protected Product $_catalogProduct;
    protected ProductRepositoryInterface $_productRepository;
    protected DataPricing $_priceHelper;
    protected Session $_customerSession;

    /**
     * Constructor
     *
     * @param PageFactory $resultPageFactory
     * @param Json $json
     * @param LoggerInterface $logger
     * @param Http $http
     */


    public function __construct(
        PageFactory $resultPageFactory,
        Json $json,
        LoggerInterface $logger,
        Http $http,
        LayoutFactory $layoutFactory,
        ProductCollectionFactory $productCollectionFactory,
        ResourceConnection $resourceConnection = null,
        SkinCareDiagnostico $skinCareDiagnostico,
        ProductRepositoryInterface $productRepository,
        DataPricing $priceHelper,
        Session $customerSession,
        \Magento\Framework\Registry $coreRegistry,
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_skinCareDiagnostico= $skinCareDiagnostico;
        $this->resultPageFactory = $resultPageFactory;
        $this->serializer = $json;
        $this->logger = $logger;
        $this->http = $http;
        $this->layoutFactory = $layoutFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->_productRepository = $productRepository;
        $this->_storeManager = $storeManager;
        $this->_priceHelper = $priceHelper;
        $this->_customerSession = $customerSession;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
        $this->resourceConnection = $resourceConnection
            ?? ObjectManager::getInstance()->create(ResourceConnection::class);
    }

    public function getDiagnostico(){

        $result = $this->simulador();
        #$result = $this->_skinCareDiagnostico->getProductCollection();
        return $result;
    }

    public function simulador(){

        $connection = $this->resourceConnection->getConnection();
        $incomingValue = 89;#$this->getRequest()->getParam("value");

        $scoreDiagnostico = $incomingValue;
        $type = 'texture';#$this->getRequest()->getParam("type");
        $incomingValue = $incomingValue / 10;
        $attrCodeMin = "{$type}_score_min";
        $attrCodeMax = "{$type}_score_max";
        $minValue = $this->getOptionId($attrCodeMin, floor($incomingValue) * 10, $connection);
        $maxValue = $this->getOptionId($attrCodeMax, ceil($incomingValue) * 10, $connection);
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addAttributeToSelect("*");
        $productCollection->addAttributeToFilter($attrCodeMin, $minValue);
        $productCollection->addAttributeToFilter($attrCodeMax, $maxValue);
        $productCollection->setPageSize(20);

        if($productCollection->getSize() < 1) {
            echo ""; die();
        }

        $items= $productCollection->getData();
        $r2 = $this->_coreRegistry->registry('diagnostico');

        $result= [];
        $type = 'wrinkle';
        $productInfo = [];

        $result['test'] = $r2;

        $result['dark_circle']= [];
        $result['wrinkle']= [];
        $result['texture']= [];
        $result['spot']= [];

        $result['results']['dark_circle'] = 0;
        $result['results']['texture'] = 0;
        $result['results']['wrinkle'] = 0;
        $result['results']['spot'] = 0;

        $store = $this->_storeManager->getStore();

        if($type == 'dark_circle'):
            $result['results']['dark_circle'] = $scoreDiagnostico;
        endif;
        if($type == 'texture'):
            $result['results']['texture'] = $scoreDiagnostico;
        endif;
        if($type == 'wrinkle'):
            $result['results']['wrinkle'] = $scoreDiagnostico;
        endif;
        if($type == 'spot'):
            $result['results']['spot'] = $scoreDiagnostico;
        endif;


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

        return $result;
    }

    /**
     * @param $attributeCode
     * @param $value
     * @param AdapterInterface $connection
     * @return string
     */
    private function getOptionId($attributeCode, $value, AdapterInterface $connection)
    {
        $sql = "select eaov.option_id
            from eav_attribute ea
                inner join eav_attribute_option eao ON ea.attribute_id = eao.attribute_id
                inner join eav_attribute_option_value eaov ON eaov.option_id = eao.option_id
            where attribute_code = '$attributeCode' and eaov.value = '" .
            str_replace("'", "\\'", $value) . "';";
        return $connection->fetchOne($sql);
    }

}

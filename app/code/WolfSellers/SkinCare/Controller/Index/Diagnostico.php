<?php
/**
 * Copyright © SkinCare All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace WolfSellers\SkinCare\Controller\Index;

use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResourceConnection;
use WolfSellers\SkinCare\Block\Widget\ProductList;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;
use Magento\Customer\Model\Session as CustomerSession;
use WolfSellers\SkinCare\Model\Source\SkinCareDiagnostico;

class Diagnostico extends Action
{

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
    protected CustomerSession  $_customerSession;
    protected SkinCareDiagnostico $_skinCareDiagnostico;

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
        SkinCareDiagnostico $skinCareDiagnostico,
        Context $context,
        ResourceConnection $resourceConnection = null

    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->serializer = $json;
        $this->logger = $logger;
        $this->http = $http;
        $this->layoutFactory = $layoutFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->_skinCareDiagnostico = $skinCareDiagnostico;
        parent::__construct($context);
        $this->resourceConnection = $resourceConnection
            ?? ObjectManager::getInstance()->create(ResourceConnection::class);
    }

    /**
     * Execute view action
     */
    public function execute()
    {
        $connection = $this->resourceConnection->getConnection();
        $incomingValue = 89;#$this->getRequest()->getParam("value");

        $scoreDiagnostico = $incomingValue;
        $type = 'wrinkle';#$this->getRequest()->getParam("type");
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
        if ($productCollection->getSize() < 1) {
            echo "";
            die();
        }


        $r1 = $this->_skinCareDiagnostico->setProductCollection($productCollection, $type, $scoreDiagnostico);
        $r2 = $this->_skinCareDiagnostico->getProductCollection();
        #$resultPage = $this->resultPageFactory->create();
        #$block = $resultPage->getLayout()->getBlock('SkinCareDiagnostico');
        #$block->setData('diagnostico', 'll');
        print_r($r2);
        exit;


        return print('ok');

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

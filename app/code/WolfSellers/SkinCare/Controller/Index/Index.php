<?php
/**
 * Copyright © SkinCare All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace WolfSellers\SkinCare\Controller\Index;

use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Customer\Model\Session as customerSession;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\View\Result\PageFactory;


use WolfSellers\SkinCare\Block\Widget\ProductList;

use WolfSellers\SkinCare\Model\SimulatorFactory;
use WolfSellers\SkinCare\Model\SimulatorRepository;

class Index extends Action
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
     * @var Http
     */
    protected $http;
    private LayoutFactory $layoutFactory;
    private ProductCollectionFactory $productCollectionFactory;
    private RequestInterface $request;
    private ResourceConnection $resourceConnection;
    protected customerSession $customerSession;
    private SimulatorFactory $simulatorFactory;
    private SimulatorRepository $simulatorRepository;


    public function __construct(
        PageFactory $resultPageFactory,
        Json $json,
        Http $http,
        LayoutFactory $layoutFactory,
        ProductCollectionFactory $productCollectionFactory,
        customerSession $customerSession,
        Context $context,
        ResourceConnection $resourceConnection = null,
        SimulatorFactory $simulatorFactory,
        SimulatorRepository $simulatorRepository
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->serializer = $json;
        $this->http = $http;
        $this->layoutFactory = $layoutFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->customerSession = $customerSession;
        parent::__construct($context);
        $this->resourceConnection = $resourceConnection
            ?? ObjectManager::getInstance()->create(ResourceConnection::class);
        $this->simulatorFactory = $simulatorFactory;
        $this->simulatorRepository = $simulatorRepository;
    }

    /**
     * Execute view action
     */
    public function execute()
    {
        $connection = $this->resourceConnection->getConnection();
        $incomingValueParam = $this->getRequest()->getParam("value");
        $type = $this->getRequest()->getParam("type");
        $formId = $this->getRequest()->getParam("form");
        $incomingValue = $incomingValueParam / 10;
        $attrCodeMin = "{$type}_score_min";
        $attrCodeMax = "{$type}_score_max";
        $minValue = $this->getOptionId($attrCodeMin, floor($incomingValue) * 10, $connection);
        $maxValue = $this->getOptionId($attrCodeMax, ceil($incomingValue) * 10, $connection);

        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addAttributeToSelect("*");
        $productCollection->addAttributeToFilter($attrCodeMin, $minValue);
        $productCollection->addAttributeToFilter($attrCodeMax, $maxValue);
        $productCollection->setPageSize(20);

        $this->setSessionVariables($type, $productCollection, $incomingValueParam, $formId);
        if($productCollection->getSize() < 1) {
            echo ""; die();
        }

        $conditions = [
            $attrCodeMin => $minValue,
            $attrCodeMax => $maxValue
        ];
        //die("\$minDark = [$minDark] -- \$maxDark = [$maxDark]<pre>" . print_r($productCollection->getAllIds(), true));
        /** @var ProductList $productBlock */
        $productBlock = $this->layoutFactory->create()
            ->createBlock(
                ProductList::class
            );
        $productBlock->setData("anchor_text", "");
        $productBlock->setData("id_path", "");
        $productBlock->setData("show_pager", "0");
        $productBlock->setData("products_count", "20");
        $productBlock->setData("condition_option", "sku");
        $productBlock->setData("condition_option_value", "");
        $productBlock->setData("conditions_encoded", "[]");
        $productBlock->setData("conditions", json_encode($conditions));
        $productBlock->setData("sort_order", "position_by_sku");
        $productBlock->setProductCollection($productCollection);
        $productBlock->setTemplate("Magento_PageBuilder::catalog/product/widget/content/carousel.phtml");
        echo '<div data-content-type="products-' . $type. '" data-appearance="carousel" data-autoplay="false" data-autoplay-speed="4000" data-infinite-loop="false" data-show-arrows="true" data-show-dots="true" data-carousel-mode="default" data-center-padding="90px" data-element="main">';


        echo $productBlock->toHtml();
        echo '</div>
</div></div>
<div data-content-type="row" data-appearance="contained" data-element="main">
<div class="result-slider slider-wrinkles" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" data-pb-style="VSELS4O">
<div data-content-type="text" data-appearance="default" data-element="main">
<h2>&nbsp;</h2>
<script type="text/x-magento-init">
{"*":{"Magento_PageBuilder/js/widget-initializer":{"config":{"[data-content-type=\"products-' . $type. '\"][data-appearance=\"carousel\"]":{"Amasty_Xsearch/js/content-type/products/appearance/carousel/widget-override":false}},"breakpoints":{"desktop":{"label":"Desktop","stage":true,"default":true,"class":"desktop-switcher","icon":"Magento_PageBuilder::css/images/switcher/switcher-desktop.svg","conditions":{"min-width":"1024px"},"options":{"products":{"default":{"slidesToShow":"4"}}}},"tablet":{"conditions":{"max-width":"1024px","min-width":"768px"},"options":{"products":{"default":{"slidesToShow":"4"},"continuous":{"slidesToShow":"3"}}}},"mobile":{"label":"Mobile","stage":true,"class":"mobile-switcher","icon":"Magento_PageBuilder::css/images/switcher/switcher-mobile.svg","media":"only screen and (max-width: 768px)","conditions":{"max-width":"768px","min-width":"640px"},"options":{"products":{"default":{"slidesToShow":"3"}}}},"mobile-small":{"conditions":{"max-width":"640px"},"options":{"products":{"default":{"slidesToShow":"2"},"continuous":{"slidesToShow":"1"}}}},"mobile-tiny":{"conditions":{"max-width":"480px"},"options":{"products":{"default":{"slidesToShow":"2"},"continuous":{"slidesToShow":"2"}}}}}}}}
</script>';
        die();

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

    /**
     *
     * @param string $type
     * @param ProductCollection $productCollection
     * @param $incomingValue
     * @return void
     */
    private function setSessionVariables(string $type, ProductCollection $productCollection, $incomingValue, $formId){
        try{
            $simulator = $this->simulatorRepository->getByFormType($formId,$type);
        }catch (\Exception $exception){
            $simulator = $this->simulatorFactory->create();
        }

        try {
            $simulator->setType($type);
            $simulator->setPercentage($incomingValue);
            $simulator->setProductIds($this->getProductIdsFromCollection($productCollection));
            $simulator->setFormId($formId);
            $this->simulatorRepository->save($simulator);
        }catch (\Exception $exception){}
    }

    /**
     * @param ProductCollection $productCollection
     * @return String
     */
    private function getProductIdsFromCollection(ProductCollection $productCollection){
        $productIds = [];
        foreach ($productCollection->getData() as $product){
            $productIds[] = $product['entity_id'];
        }
        return $this->serializer->serialize($productIds);
    }
}

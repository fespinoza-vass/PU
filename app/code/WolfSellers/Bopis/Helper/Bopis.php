<?php

namespace WolfSellers\Bopis\Helper;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Session;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\InventoryApi\Api\GetSourceItemsBySkuInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use WolfSellers\Bopis\Api\BopisRepositoryInterface;

class Bopis extends AbstractHelper
{
    CONST NOT_ALLOWED = ["online_CO","online_CR","online_PE", "default"];

    private BopisRepositoryInterface $bopisRepository;
    private Session $checkoutSession;
    private JsonFactory $jsonFactory;
    private RequestInterface $request;
    private GetSourceItemsBySkuInterface $sourceItemsBySku;
    private \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder;
    private SourceRepositoryInterface $sourceRepository;
    private Data $data;
    private PageFactory $pageFactory;
    private \Magento\Framework\Registry $_coreRegistry;
    private ProductRepositoryInterface $productRepository;
    private \Magento\Catalog\Model\Session $catalogSession;
    private LoggerInterface $logger;
    private Context $context;
    private \Magento\Store\Model\StoreManagerInterface $storeManager;
    private \Magento\Directory\Model\RegionFactory $regionFactory;

    /**
     * @param Session $checkoutSession
     * @param BopisRepositoryInterface $bopisRepository
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param RequestInterface $request
     * @param GetSourceItemsBySkuInterface $sourceItemsBySku
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SourceRepositoryInterface $sourceRepository
     * @param Data $data
     * @param PageFactory $pageFactory
     * @param Registry $_coreRegistry
     * @param ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Model\Session $catalogSession
     * @param StoreManagerInterface $storeManager
     * @param RegionFactory $regionFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        Session $checkoutSession,
        BopisRepositoryInterface $bopisRepository,
        Context $context,
        JsonFactory $jsonFactory,
        RequestInterface $request,
        GetSourceItemsBySkuInterface $sourceItemsBySku,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        SourceRepositoryInterface $sourceRepository,
        Data $data,
        PageFactory $pageFactory,
        \Magento\Framework\Registry $_coreRegistry,
        ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        LoggerInterface $logger
    ){
        parent::__construct($context);
        $this->bopisRepository = $bopisRepository;
        $this->checkoutSession = $checkoutSession;
        $this->jsonFactory = $jsonFactory;
        $this->request = $request;
        $this->sourceItemsBySku = $sourceItemsBySku;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sourceRepository = $sourceRepository;
        $this->data = $data;
        $this->pageFactory = $pageFactory;
        $this->_coreRegistry = $_coreRegistry;
        $this->productRepository = $productRepository;
        $this->catalogSession = $catalogSession;
        $this->logger = $logger;
        $this->context = $context;
        $this->storeManager = $storeManager;
        $this->regionFactory = $regionFactory;
    }

    public function isBopis(): bool
    {

        try {
            $bopis = $this->bopisRepository->getByQuoteId($this->checkoutSession->getQuoteId());
            if($bopis->getType() == "store-pickup") {
                return true;
            }
        } catch (LocalizedException $e) {}
        return false;
    }

    public function getSources($form = false, $count = false){
        $jsonResponse = $this->jsonFactory->create();
        $sku = $this->getSku();
        $items = array_map(function($item){ return $item->getSku(); }, $this->checkoutSession->getQuote()->getAllVisibleItems());
        $items[] = $this->request->getParam("sku") ? $this->request->getParam("sku") : ($sku ?: "");
        $lat = $this->request->getParam("lat");
        $lng = $this->request->getParam("lng");
        $items = array_unique($items);

        if (sizeof($items) > 0){
            $data = [];
            foreach ($items as $item) {
                $qty = 0;
                if ($product = $this->getProduct($item)){
                    $quoteItem = $this->checkoutSession->getQuote()->getItemByProduct($product);
                    if ($quoteItem){
                        $qty = $quoteItem->getQty();
                    }
                }

                foreach ($this->sourceItemsBySku->execute($item) as $source) {
                    if ($source->getStatus() == 1 AND $source->getQuantity() >= $qty){
                        $data[] = $source->getSourceCode();
                    }
                }
            }

            $commonSources = [];

            foreach (array_count_values($data) as $k => $v) {
                if ($v == sizeof($items)){
                    $commonSources[] = $k;
                }
            }

            $search = $this->searchCriteriaBuilder
                ->addFilter("source_code", implode(",", $commonSources), "in")
                ->addFilter("source_code", implode(",", self::NOT_ALLOWED), "nin")
                ->create();
            $sourceData = $this->sourceRepository->getList($search);
        }else{
            $search = $this->searchCriteriaBuilder
                ->addFilter("source_code", implode(",", self::NOT_ALLOWED), "nin")
                ->create();
            $sourceData = $this->sourceRepository->getList($search);
        }

        $product = $this->getProduct(current($items));

        if($product->getTypeId() == 'bundle'){
            $sourcesBundle =  $this->getAvailableSourcesBundleProduct($product);

            $criteria = $this->searchCriteriaBuilder
                ->addFilter("source_code", implode(",",array_keys($sourcesBundle)), "in")
                ->addFilter("source_code", implode(",", self::NOT_ALLOWED), "nin")
                ->create();

            $sourceData = $this->sourceRepository->getList($criteria);
        }


        $sources = $sourceData->getItems();

        $currentCountry = $this->data->getCurrentCountry();

        $sourceByCountry = [];

        foreach ($sources as $source) {
            if (!$source->isEnabled() || $source->getCountryId() != $currentCountry || !$source->getIsPickupLocationActive()) {
                continue;
            }

            $sourceByCountry[] = $source->getData();
        }

        if ($lat AND $lng AND sizeof($sourceByCountry) > 1){
            $sourceByCountry = $this->sortPoints($lat, $lng, $sourceByCountry);
        }

        $this->checkoutSession->setCanBuy(sizeof($sourceByCountry) > 0);

        if ($form){
            $resultPage = $this->pageFactory->create();
            $response = $resultPage->getLayout()
                ->createBlock('Magento\Customer\Block\Form\Register')
                ->setTemplate('WolfSellers_Bopis::sources/grid.phtml')
                ->setData('states', $this->prepareSourcesByState($sourceByCountry))
                ->toHtml();
            $jsonResponse->setData([
                "sources" => $response,
                "count" => sizeof($sourceByCountry)
            ]);
        }

        $jsonResponse->setStatusHeader(200);

        return $jsonResponse;
    }

    /**
     * @param $sourceByCountry
     * @return array
     */
    public function prepareSourcesByState($sourceByCountry)
    {
        $formattedSources = [];
        $formattedSources2 = [];

        foreach ($sourceByCountry as $source) {
            if ($this->storeManager->getStore()->getId() == 19){
                $region = $this->regionFactory->create()->load($source['region_id']);
                $formattedSources[$region->getName()][$source['name']] = [
                    'source_code' => $source['source_code'],
                    'name' => $source['name']
                ];
                asort($formattedSources[$region->getName()]);

            }elseif ($this->storeManager->getStore()->getId() == 6){
                $formattedSources[$source['city']][$source['name']] = [
                    'source_code' => $source['source_code'],
                    'name' => $source['name']
                ];
                asort($formattedSources[$source['city']]);
            }
        }

        if ($this->storeManager->getStore()->getId() == 6){
            foreach ($formattedSources AS $index => $value){
                $city = explode(',',$index);
                $formattedSources2[$city[0]] = $value;
            }
            ksort($formattedSources2);
            return $formattedSources2;
        }elseif ($this->storeManager->getStore()->getId() == 19){
            ksort($formattedSources);
            return $formattedSources;
        }
    }

    public function getSku() {
        $productId = $this->catalogSession->getData('last_viewed_product_id');
        if ($productId) {
            $product = $this->productRepository->getById($productId);
            return $product->getSku();
        }

        return false;
    }

    protected function getProduct($sku){
        try{
            return $this->productRepository->get($sku);
        }catch (\Exception $exception){
            return null;
        }
    }

    private function getDistanceBetweenPoints($latitude1, $longitude1, $latitude2, $longitude2): float{
        $theta = $longitude1 - $longitude2;
        $distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
        $distance = acos($distance);
        $distance = rad2deg($distance);
        $distance = $distance * 60 * 1.1515;
        $distance = $distance * 1.609344;
        return (round($distance,8));
    }

    private function sortPoints($originLat, $originLng , $points): array
    {
        $distances = [];
        $sorted = [];
        foreach ($points as $key => $point)
        {
            $distances[$key] = $this->getDistanceBetweenPoints($originLat, $originLng, $point['latitude'], $point['longitude']);
        }
        asort($distances);

        foreach ($distances as $k => $v) {
            $sorted[] = $points[$k];
        }
        return $sorted;
    }

    public function getAvailableSourcesBundleProduct($product){

        $selectionCollection = $product->getTypeInstance(true)->getSelectionsCollection(
            $product->getTypeInstance(true)->getOptionsIds($product),
            $product
        );

        $sourceAvailable = array();

        foreach ($selectionCollection as $child) {
            $sources = $this->sourceItemsBySku->execute($child->getSku());
            $currentCountry = $this->data->getCurrentCountry();

            foreach ($sources as $source) {
                $_source = $this->data->getSource($source->getSourceCode());

                if (!$_source->isEnabled() || $_source->getCountryId() != $currentCountry) {
                    continue;
                }

                if(intval($child->getSelectionQty()) <= $source->getQuantity()) {
                    $sourceAvailable[$child->getSku()][$source->getSourceCode()] = true;
                }
            }
        }

        return call_user_func_array('array_intersect_key', $sourceAvailable);
    }
}

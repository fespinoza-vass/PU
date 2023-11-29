<?php

namespace WolfSellers\InStorePickup\Model\SearchResult\Strategy;

use Magento\Framework\Api\SortOrder;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\Data\SourceSearchResultsInterface;
use Magento\InventoryInStorePickup\Model\SearchRequest\Area\GetDistanceToSources;
use Magento\InventoryInStorePickupApi\Api\Data\SearchRequest\AreaInterface;
use Magento\InventoryInStorePickupApi\Api\Data\SearchRequestInterface;
use Magento\InventoryInStorePickupApi\Model\SearchResult\StrategyInterface;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\InventoryApi\Api\SourceItemRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use WolfSellers\InStorePickup\Helper\SplitCart;


/**
 * @class StockDistanceBased
 */
class CustomRules implements StrategyInterface
{
    const XML_PATH_STRATEGY_SELECTION = 'instore_pickup/strategy/selection';

    const STRATEGY_TYPE = 'custom_rules';

    /**
     * @var GetDistanceToSources
     */
    private $getDistanceToSources;

    /**
     * @var GetProductSalableQtyInterface
     */
    private $getProductSalableQty;

    /**
     * @var StockResolverInterface
     */
    private $stockResolver;


    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;


    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var SourceItemRepositoryInterface
     */
    private $sourceItemRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    private SplitCart $splitCartHelper;

    /**
     * @param GetDistanceToSources $getDistanceToSources
     * @param GetProductSalableQtyInterface $getProductSalableQty
     * @param StockResolverInterface $stockResolver
     * @param ScopeConfigInterface $scopeConfig
     * @param CheckoutSession $checkoutSession
     * @param SourceItemRepositoryInterface $sourceItemRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SplitCart $splitCartHelper
     */
    public function __construct(
        GetDistanceToSources $getDistanceToSources,
        GetProductSalableQtyInterface $getProductSalableQty,
        StockResolverInterface $stockResolver,
        ScopeConfigInterface $scopeConfig,
        CheckoutSession $checkoutSession,
        SourceItemRepositoryInterface $sourceItemRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SplitCart $splitCartHelper
    ) {
        $this->getDistanceToSources = $getDistanceToSources;
        $this->getProductSalableQty = $getProductSalableQty;
        $this->stockResolver = $stockResolver;
        $this->scopeConfig = $scopeConfig;
        $this->checkoutSession = $checkoutSession;
        $this->sourceItemRepository = $sourceItemRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->splitCartHelper = $splitCartHelper;
    }

    /**
     * @inheritdoc
     */
    public function getSources(
        SearchRequestInterface $searchRequest,
        SourceSearchResultsInterface $sourcesSearchResult
    ): array {
        $sortOrder = $this->getDistanceSort($searchRequest);
        $distanceToSources = $this->getDistanceToSources->execute($searchRequest->getArea());
        $sources = $sourcesSearchResult->getItems();

        try {
            $items = $this->checkoutSession->getQuote()->getAllVisibleItems();
            $productSkus = array_map(function ($item) {
                return $item->getProduct()->getSku();
            }, $items);
        } catch (LocalizedException $e) {
            return $sources;
        }

        $sources = array_filter($sources, function (SourceInterface $source) use ($productSkus) {
            foreach ($productSkus as $sku) {
                $searchCriteria = $this->searchCriteriaBuilder
                    ->addFilter('sku', $sku)
                    ->addFilter('source_code', $source->getSourceCode())
                    ->create();

                $sourceItems = $this->sourceItemRepository->getList($searchCriteria)->getItems();

                foreach ($sourceItems as $sourceItem) {
                    if ($sourceItem->getQuantity() <= 0) {
                        return false;
                    }
                }
            }

            return true;
        });

        if ($sortOrder) {
            $sources = $this->sortSourcesByDistance($sources, $distanceToSources, $sortOrder->getDirection());
        }

        if ($searchRequest->getSort() === null) {
            $sources = $this->sortSourcesByDistance($sources, $distanceToSources);
        }

        return array_values($sources);
    }

    /**
     * Sort Sources by Distance.
     *
     * @param array $sources
     * @param array $distanceToSources
     * @param string $sortDirection
     *
     * @return array
     */
    private function sortSourcesByDistance(
        array $sources,
        array $distanceToSources,
        string $sortDirection = SortOrder::SORT_ASC
    ): array {
        $ascSort = function (SourceInterface $left, SourceInterface $right) use ($distanceToSources) {
            return $distanceToSources[$left->getSourceCode()] <=> $distanceToSources[$right->getSourceCode()];
        };

        $descSort = function (SourceInterface $left, SourceInterface $right) use ($distanceToSources) {
            return $distanceToSources[$right->getSourceCode()] <=> $distanceToSources[$left->getSourceCode()];
        };

        $sort = $sortDirection === SortOrder::SORT_ASC ? $ascSort : $descSort;

        usort($sources, $sort);

        return $sources;
    }

    /**
     * Get distance Sort from list of Sorts.
     *
     * @param SearchRequestInterface $searchRequest
     *
     * @return SortOrder|null
     */
    private function getDistanceSort(SearchRequestInterface $searchRequest): ?SortOrder
    {
        $sorts = $searchRequest->getSort();

        if ($sorts === null) {
            return null;
        }

        foreach ($sorts as $sortOrder) {
            if ($sortOrder->getField() === AreaInterface::DISTANCE_FIELD) {
                return $sortOrder;
            }
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function isApplicable(
        SearchRequestInterface $searchRequest,
        SourceSearchResultsInterface $sourcesSearchResult
    ): bool {
        $isAreaAvailable = (bool)$searchRequest->getArea();
        $selectedStrategy = $this->scopeConfig->getValue(self::XML_PATH_STRATEGY_SELECTION);
        $isSplitCart = $this->splitCartHelper->isSplitCart();

        return $isAreaAvailable && $selectedStrategy === self::STRATEGY_TYPE && !$isSplitCart;
    }
}

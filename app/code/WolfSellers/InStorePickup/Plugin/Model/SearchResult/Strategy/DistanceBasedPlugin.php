<?php
namespace WolfSellers\InStorePickup\Plugin\Model\SearchResult\Strategy;

use Magento\InventoryInStorePickup\Model\SearchResult\Strategy\DistanceBased;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\InventoryInStorePickupApi\Api\Data\SearchRequestInterface;
use Magento\InventoryApi\Api\Data\SourceSearchResultsInterface;
use WolfSellers\InStorePickup\Helper\SplitCart;

/**
 * @class DistanceBasedPlugin
 */
class DistanceBasedPlugin
{
    const XML_PATH_STRATEGY_SELECTION = 'instore_pickup/strategy/selection';

    const STRATEGY_TYPE = 'distance_based';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var SplitCart
     */
    private $splitCartHelper;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param SplitCart $splitCartHelper
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        SplitCart $splitCartHelper
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->splitCartHelper = $splitCartHelper;
    }


    /**
     * Plugin for isApplicable method.
     *
     * @param DistanceBased $subject
     * @param bool $result
     * @param SearchRequestInterface $searchRequest
     * @param SourceSearchResultsInterface $sourcesSearchResult
     * @return bool
     */
    public function afterIsApplicable(
        DistanceBased $subject,
        bool $result,
        SearchRequestInterface $searchRequest,
        SourceSearchResultsInterface $sourcesSearchResult
    ): bool {
        if (!$searchRequest->getArea()) {
            return false;
        }

        $selectedStrategy = $this->scopeConfig->getValue(self::XML_PATH_STRATEGY_SELECTION);
        $isSplitCart = $this->splitCartHelper->isSplitCart();

        return $result && $selectedStrategy === self::STRATEGY_TYPE || $isSplitCart;
    }
}

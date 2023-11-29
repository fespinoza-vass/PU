<?php
namespace WolfSellers\InStorePickup\Plugin\Model\SearchResult\Strategy;

use Magento\InventoryInStorePickup\Model\SearchResult\Strategy\DistanceBased;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\InventoryInStorePickupApi\Api\Data\SearchRequestInterface;
use Magento\InventoryApi\Api\Data\SourceSearchResultsInterface;
use WolfSellers\InStorePickup\Helper\SplitCart;
use Magento\InventoryInStorePickupApi\Api\Data\SearchRequest\AreaInterface;
use Magento\InventoryInStorePickupApi\Api\Data\SearchRequest\AreaInterfaceFactory;

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
    private SplitCart $splitCartHelper;
    private AreaInterfaceFactory $areaFactory;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param SplitCart $splitCartHelper
     * @param AreaInterfaceFactory $areaFactory
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        SplitCart $splitCartHelper,
        AreaInterfaceFactory $areaFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->splitCartHelper = $splitCartHelper;
        $this->areaFactory = $areaFactory;
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
        $selectedStrategy = $this->scopeConfig->getValue(self::XML_PATH_STRATEGY_SELECTION);
        $isSplitCart = $this->splitCartHelper->isSplitCart();

        return $result && $selectedStrategy === self::STRATEGY_TYPE || $isSplitCart;
    }
}

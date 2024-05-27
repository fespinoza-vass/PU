<?php

namespace WolfSellers\Bopis\Plugin\InventorySales\Model\IsProductSalableForRequestedQtyCondition;

use Closure;
use Magento\Catalog\Model\ProductRepository;
use Magento\Checkout\Model\Session;
use Magento\InventoryApi\Api\GetSourceItemsBySkuInterface;
use Magento\InventoryConfigurationApi\Api\Data\StockItemConfigurationInterface;
use Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface;
use Magento\InventoryReservationsApi\Model\GetReservationsQuantityInterface;
use Magento\InventorySales\Model\IsProductSalableForRequestedQtyCondition\IsSalableWithReservationsCondition;
use Magento\InventorySalesApi\Api\Data\ProductSalabilityErrorInterfaceFactory;
use Magento\InventorySalesApi\Api\Data\ProductSalableResultInterface;
use Magento\InventorySalesApi\Api\Data\ProductSalableResultInterfaceFactory;
use Magento\InventorySalesApi\Api\IsProductSalableForRequestedQtyInterface;
use Magento\InventorySalesApi\Model\GetStockItemDataInterface;
use WolfSellers\Bopis\Api\BopisRepositoryInterface;
use WolfSellers\Bopis\Helper\Config;

class IsSalableWithReservationsConditionPlugin
{

    private GetReservationsQuantityInterface $getReservationsQuantity;
    private GetStockItemConfigurationInterface $getStockItemConfiguration;
    private ProductSalabilityErrorInterfaceFactory $productSalabilityErrorFactory;
    private ProductSalableResultInterfaceFactory $productSalableResultFactory;
    private BopisRepositoryInterface $bopisRepository;
    private GetSourceItemsBySkuInterface $sourceItemsBySku;
    private Session $checkoutSession;
    private $bopis;
    private Config $config;
    private \Magento\Catalog\Model\ProductRepository $productRepository;

    /**
     * @param GetReservationsQuantityInterface $getReservationsQuantity
     * @param GetStockItemConfigurationInterface $getStockItemConfiguration
     * @param ProductSalabilityErrorInterfaceFactory $productSalabilityErrorFactory
     * @param ProductSalableResultInterfaceFactory $productSalableResultFactory
     * @param BopisRepositoryInterface $bopisRepository
     * @param GetSourceItemsBySkuInterface $sourceItemsBySku
     * @param Session $checkoutSession
     * @param Config $config
     * @param ProductRepository $productRepository
     */
    public function __construct(
        GetReservationsQuantityInterface $getReservationsQuantity,
        GetStockItemConfigurationInterface $getStockItemConfiguration,
        ProductSalabilityErrorInterfaceFactory $productSalabilityErrorFactory,
        ProductSalableResultInterfaceFactory $productSalableResultFactory,
        BopisRepositoryInterface $bopisRepository,
        GetSourceItemsBySkuInterface $sourceItemsBySku,
        Session $checkoutSession,
        Config $config,
        \Magento\Catalog\Model\ProductRepository $productRepository
    )
    {
        $this->getReservationsQuantity = $getReservationsQuantity;
        $this->getStockItemConfiguration = $getStockItemConfiguration;
        $this->productSalabilityErrorFactory = $productSalabilityErrorFactory;
        $this->productSalableResultFactory = $productSalableResultFactory;
        $this->bopisRepository = $bopisRepository;
        $this->sourceItemsBySku = $sourceItemsBySku;
        $this->checkoutSession = $checkoutSession;
        $this->config = $config;
        $this->productRepository = $productRepository;
    }

    public function aroundExecute(
        IsSalableWithReservationsCondition $subject,
        Closure $proceed,
        string $sku,
        int $stockId,
        float $requestedQty
    ): ProductSalableResultInterface {

        $product = $this->productRepository->get($sku);
        if ($product->getTypeId() == "bundle") {
            return $proceed($sku, $stockId, $requestedQty);
        }

        if (!$this->config->isActive()){
            return $proceed($sku, $stockId, $requestedQty);
        }


        $this->setBopis();

        if ($this->canContinue($requestedQty, $sku)){
            return $proceed($sku, $stockId, $requestedQty);
        }

        $errors = [
            $this->productSalabilityErrorFactory->create([
                'code' => 'is_salable_with_reservations-not_enough_qty',
                'message' => __('The requested qty is not available')
            ])
        ];
        return $this->productSalableResultFactory->create(['errors' => $errors]);
    }

    protected function canContinue($qty,$sku){
        if ($this->bopis == null){
            return true;
        }
        foreach ($this->sourceItemsBySku->execute($sku) as $source) {
            if ($source->getSourceCode() == $this->bopis->getStore() AND (int)$source->getQuantity() >= $qty){
                return true;
            }
        }
        return false;
    }

    private function setBopis(){
        if ($this->bopis != null) return;

        try{
            $this->bopis = $this->bopisRepository->getByQuoteId($this->checkoutSession->getQuoteId());
        }catch (\Exception $exception){
            $this->bopis = null;
        }
    }
}

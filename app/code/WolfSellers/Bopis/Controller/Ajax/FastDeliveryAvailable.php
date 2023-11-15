<?php

namespace WolfSellers\Bopis\Controller\Ajax;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\InventoryApi\Api\GetSourceItemsBySkuInterface;
use Magento\Checkout\Model\Session;
use WolfSellers\Bopis\Model\ResourceModel\AbstractBopisCollection;

class FastDeliveryAvailable implements HttpGetActionInterface
{
    /** @var int  */
    const MINIMUM_SALABLE_QUANTITY = 2;

    /**
     * @param JsonFactory $jsonResultFactory
     * @param Session $_checkout
     * @param GetSourceItemsBySkuInterface $sourceItemsBySku
     */
    public function __construct(
        protected JsonFactory                 $jsonResultFactory,
        protected Session                     $_checkout,
        protected GetSourceItemsBySkuInterface $sourceItemsBySku
    )
    {
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\InventoryConfigurationApi\Exception\SkuIsNotAssignedToStockException
     */
    public function execute()
    {
        $items = $this->_checkout->getQuote()->getAllVisibleItems();

        $saleableQuatity = 1;

        foreach ($items as $item) {
            $max = $this->getMaxQtyPerSource($item->getSku());


            if ($max < self::MINIMUM_SALABLE_QUANTITY){
                $saleableQuatity = 0;
            }
        }

        $result = $this->jsonResultFactory->create();
        $data = ['available' => "$saleableQuatity"];
        $result->setData($data);
        return $result;
    }

    /**
     * Get the maximum amount of stock
     *
     * @param $sku
     * @return int
     */
    private function getMaxQtyPerSource($sku): int
    {
        $max = 0;

        $inventory = $this->sourceItemsBySku->execute($sku);

        foreach ($inventory as $source) {
            // Exclude Default Source Lurin.
            if ($source->getSourceCode() == AbstractBopisCollection::DEFAULT_BOPIS_SOURCE_CODE) continue;

            if (!$source->getStatus()) continue;

            if ($source->getQuantity() > $max) {
                $max = $source->getQuantity();
            }
        }

        return (int)$max;
    }
}

<?php

namespace WolfSellers\Bopis\Controller\Ajax;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventoryApi\Api\GetSourceItemsBySkuInterface;
use Magento\Checkout\Model\Session;
use Magento\InventoryConfigurationApi\Exception\SkuIsNotAssignedToStockException;

/**
 * Class IsLurinOnly
 *
 * Controller action responsible for checking if products in the cart
 * are available only in the Lurin source.
 */
class IsLurinOnly implements HttpGetActionInterface
{
    /**
     * @var JsonFactory
     */
    protected $jsonResultFactory;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var GetSourceItemsBySkuInterface
     */
    protected $sourceItemsBySku;

    /**
     * Constructor
     *
     * @param JsonFactory $jsonResultFactory Factory for creating JSON result objects.
     * @param Session $checkoutSession Checkout session for accessing cart items.
     * @param GetSourceItemsBySkuInterface $sourceItemsBySku Interface to get source items by SKU.
     */
    public function __construct(
        JsonFactory $jsonResultFactory,
        Session $checkoutSession,
        GetSourceItemsBySkuInterface $sourceItemsBySku
    ) {
        $this->jsonResultFactory = $jsonResultFactory;
        $this->checkoutSession = $checkoutSession;
        $this->sourceItemsBySku = $sourceItemsBySku;
    }

    /**
     * Executes the action to check Lurin-only stock availability.
     *
     * @return ResponseInterface|Json|ResultInterface
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws SkuIsNotAssignedToStockException
     */
    public function execute()
    {
        $items = $this->checkoutSession->getQuote()->getAllVisibleItems();
        $isLurinOnlyAvailable = true;

        foreach ($items as $item) {
            $maxQuantityInLurin = $this->getMaxQtyPerSource($item->getSku());

            // If any item has less than the MINIMUM_SALABLE_QUANTITY in Lurin,
            // or is available in another source, mark as false and stop checking the rest.
            if ($maxQuantityInLurin == 0) {
                $isLurinOnlyAvailable = false;
                break; // No need to check further, as we already found an item that isn't Lurin-only.
            }
        }

        $result = $this->jsonResultFactory->create();
        $result->setData(['available' => $isLurinOnlyAvailable ? '1' : '0']);

        return $result;
    }

    /**
     * Retrieves the maximum quantity of a SKU in Lurin source.
     *
     * @param string $sku SKU of the product to check.
     * @return int Maximum quantity available in Lurin.
     */
    private function getMaxQtyPerSource(string $sku): int
    {
        $maxQuantity = 0;
        $isOnlyInLurin = true;

        $inventory = $this->sourceItemsBySku->execute($sku);

        foreach ($inventory as $sourceItem) {
            if (!$sourceItem->getStatus()) {
                continue;
            }

            if ($sourceItem->getSourceCode() != 1) {
                // Found stock in another source, not Lurin.
                if ($sourceItem->getQuantity() > 0) {
                    $isOnlyInLurin = false;
                    break;
                }
            } elseif ($sourceItem->getQuantity() > $maxQuantity) {
                // Update max if this is the Lurin source and has more quantity.
                $maxQuantity = $sourceItem->getQuantity();
            }
        }

        // If stock is found in sources other than Lurin, set max to 0.
        return $isOnlyInLurin ? $maxQuantity : 0;
    }
}

<?php

declare(strict_types=1);

namespace WolfSellers\InStorePickup\Plugin;

use WolfSellers\AmastyLabel\Helper\DynamicTagRules;

/**
 *
 */
class CollectRatesPlugin
{
    /** @var DynamicTagRules */
    protected $_dynamicTagRules;

    /** @var int */
    const BOTH_LABELS = 3;

    /** @var int */
    const ONLY_INSTORE_LABEL = 2;

    /** @var int */
    const ONLY_FAST_LABEL = 1;

    /** @var int */
    const WITHOUT_LABELS = 0;

    /**
     * @param DynamicTagRules $dynamicTagRules
     */
    public function __construct(
        DynamicTagRules $dynamicTagRules
    )
    {
        $this->_dynamicTagRules = $dynamicTagRules;
    }

    /**
     * @param \Magento\InventoryInStorePickupShippingApi\Model\Carrier\InStorePickup $subject
     * @param $result
     * @param $request
     * @return mixed|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterCollectRates(
        \Magento\InventoryInStorePickupShippingApi\Model\Carrier\InStorePickup $subject,
                                                                               $result,
                                                                               $request
    )
    {
        $items = $request->getAllItems();

        $inStore = $this->hasOnlyFastItems($items);

        if (!$inStore) {
            return null;
        }

        return $result;
    }


    /**
     * Returns true if all items have the InStore Label.
     *
     * @param $items
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function hasOnlyFastItems($items): bool
    {
        $inStore = true;
        $checkPoints = [];

        foreach ($items as $item) {
            $labels = $this->_dynamicTagRules->shippingLabelsByProductSku($item->getSku());

            $fastLabel = boolval($labels['fast']);
            $inStoreLabel = boolval($labels['instore']);

            if ($fastLabel && $inStoreLabel) {
                $checkPoint = self::BOTH_LABELS;
            } elseif (!$fastLabel && $inStoreLabel) {
                $checkPoint = self::ONLY_INSTORE_LABEL;
            } elseif ($fastLabel && !$inStoreLabel) {
                $checkPoint = self::ONLY_FAST_LABEL;
            } else {
                $checkPoint = self::WITHOUT_LABELS;
            }

            if (!in_array($checkPoint, $checkPoints)) {
                $checkPoints[] = $checkPoint;
            }
        }

        // New Rules: all products with the same configuration.
        if (count($checkPoints) <= 1) {
            // "Fast Shipping Label Only" or "No Labels" will disable in-store delivery
            if (in_array(self::ONLY_FAST_LABEL, $checkPoints) ||
                in_array(self::WITHOUT_LABELS, $checkPoints)
            ) {
                $inStore = false;
            }
        }

        // New Rules: Products with different configuration.
        if (count($checkPoints) >= 2) {
            // "Fast Shipping Label Only" will disable in-store delivery
            if (in_array(self::ONLY_FAST_LABEL, $checkPoints)) {
                $inStore = false;
            }
        }

        return $inStore;
    }
}

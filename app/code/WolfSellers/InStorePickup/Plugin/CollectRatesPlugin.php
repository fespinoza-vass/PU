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

    /**
     * @param DynamicTagRules $dynamicTagRules
     */
    public function __construct(
        DynamicTagRules $dynamicTagRules
    ){
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
    ) {
        $items = $request->getAllItems();

        $inStore = $this->hasOnlyFastItems($items);

        return $inStore;
    }


    public function hasOnlyFastItems($items){
        $inStore = false;

        foreach($items as $item){
            $labels = $this->_dynamicTagRules->shippingLabelsByProductSku($item->getSku());
            if($labels['fast'] && $labels['instore']){
                $inStore =  true;
            }elseif(!$labels['fast'] && !$labels['instore']){
                $inStore =  true;
            }elseif(!$labels['fast'] && $labels['instore']){
                $inStore =  true;
            }
        }

        return $inStore;
    }
}

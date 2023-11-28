<?php

declare(strict_types=1);

namespace WolfSellers\InStorePickup\Plugin;
use WolfSellers\AmastyLabel\Helper\DynamicTagRules;

class CollectRatesPlugin
{
    /** @var DynamicTagRules */
    protected $_dynamicTagRules;

    public function __construct(
        DynamicTagRules $dynamicTagRules
    ){
        $this->_dynamicTagRules = $dynamicTagRules;
    }

    public function afterCollectRates(
        \Magento\InventoryInStorePickupShippingApi\Model\Carrier\InStorePickup $subject,
                                         $result,
                                         $request
    ) {
        $inStore = true;
        $items = $request->getAllItems();
        foreach($items as $item){
            $labels = $this->_dynamicTagRules->shippingLabelsByProductSku($item->getSku());

            if(!$labels['instore']){
                $inStore = false;
            }
        }

        if(!$inStore){
            return null;
        }

        return $result;
    }
}

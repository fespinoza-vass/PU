<?php

namespace WolfSellers\Sources\Observer\Source;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SaveAfterObserver implements ObserverInterface
{
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        $request = $observer->getData('request');
        $source = $observer->getData('source');

        $integration = $request->getParam("bopis");
        $general = $request->getParam("general");

        if (!empty($integration["available_shipping_methods"])) {
            $shippingMethods = implode(',', $integration["available_shipping_methods"]);
            $source->setData("available_shipping_methods", $shippingMethods);
        }

        $source->setData("is_fastshipping_active", $integration['extension_attributes']['is_fastshipping_active']);
        $source->setData("range_radius", $integration['extension_attributes']['range_radius']);
        $source->setData("conductor", $integration['extension_attributes']['conductor']);


        if (!empty($general["district"])) {
            $source->setData("district", $general["district"]);
        }


        if ($source->hasDataChanges()){
            $source->save();
        }
    }
}

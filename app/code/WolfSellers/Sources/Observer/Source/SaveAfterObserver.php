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

        if (!empty($integration["available_shipping_methods"])) {
            $shippingMethods = implode(',', $integration["available_shipping_methods"]);
            $source->setData("available_shipping_methods", $shippingMethods);
        }

        if ($source->hasDataChanges()){
            $source->save();
        }
    }
}

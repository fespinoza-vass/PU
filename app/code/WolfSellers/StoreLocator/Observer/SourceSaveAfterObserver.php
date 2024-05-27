<?php

namespace WolfSellers\StoreLocator\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Backend\Model\Auth\Session;

/**
 * @class SourceSaveAfterObserver
 */
class SourceSaveAfterObserver implements ObserverInterface
{
    /**
     * @param Json $_json
     * @param Session $_authSession
     */
    public function __construct(
        protected Json $_json,
        protected Session $_authSession
    ){}

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        $request = $observer->getData('request');
        $source = $observer->getData('source');

        $general = $request->getParam("general");
        $openingHours = $request->getParam("opening_hours");


        if (isset($general["store_code"])) {
            $store_id = $general["store_code"];
            $source->setData("store_code", $store_id);
        }

        if (isset($openingHours["opening_hours"])) {
            $hours = $openingHours["opening_hours"];
            $source->setData("opening_hours", $this->_json->serialize($hours));
        }

        if($source->hasDataChanges()) {
            $source->save();
        }
    }
}

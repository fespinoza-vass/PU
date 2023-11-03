<?php

declare(strict_types=1);

namespace WolfSellers\FreeShipping\Plugin;


class ShippingPlugin
{

    public function afterCollectRates(
        \Magento\Shipping\Model\Shipping $subject,
                                         $result,
                                         $request
    ) {

        $ratesResult = $subject->getResult();

        $freeShipping = false;
        foreach ($ratesResult->getAllRates() as $rate) {
            if( $rate->getCarrier() == "freeshipping" ) {
                $freeShipping = true;
            }
        }

        if($freeShipping){
            foreach ($ratesResult->getAllRates() as $rate) {
                if( $rate->getCarrier() != "freeshipping" ) {
                    $rate->setPrice(0);
                }
            }
        }

        return $result;
    }
}

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

        foreach ($ratesResult->getAllRates() as $rate) {
            if( $rate->getCarrier() == "freeshipping" ) {
                $ratesResult->reset();
                $ratesResult->append($rate);
                return $result;
            }
        }

        return $result;
    }
}

<?php
namespace WolfSellers\EnvioRapido\Plugin;

use Magento\Quote\Model\Quote\Address\Rate;

/**
 *
 */
class QuoteAddressRatePlugin
{
    /**
     * @param Rate $subject
     * @param $result
     * @param $rate
     * @return mixed
     */
    public function afterImportShippingRate(Rate $subject, $result, $rate){
        if($rate->getData('delivery_time')){
            $subject->setData('delivery_time',$rate->getData('delivery_time'));
        }
        return $result;
    }

}

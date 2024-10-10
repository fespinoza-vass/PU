<?php
 
namespace Izipay\Core\Plugin\Block\Checkout;
 
class LayoutProcessor {
 
    public function afterProcess(\Magento\Checkout\Block\Checkout\LayoutProcessor $subject, array $jsLayout) {
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                        ['payment']['children']['payments-list']['children']
                )) {
 
            foreach ($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children']['payments-list']['children'] as $key => $payment) {
 
                /* Telephone Billing Address */
                if (isset($payment['children']['form-fields']['children']['telephone'])) {
                    $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                            ['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']
                    ['telephone']['validation'] = ['required-entry' => true, 'min_text_length' => 7, 'max_text_length' => 15, 'validate-digits' => true];
                }
                
                /* Postcode Billing Address */
                /*if (isset($payment['children']['form-fields']['children']['postcode'])) {
                    $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                            ['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']
                    ['postcode']['validation'] = ['required-entry' => true, 'min_text_length' => 5, 'max_text_length' => 10];
                }*/

            }
        }
 
        return $jsLayout;
    }
 
}
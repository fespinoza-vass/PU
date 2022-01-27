<?php

namespace adobe\adobeSignCheckout\Model\Config\Source;
use \Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Profile
 * @package Vendor\Package\Model\Config\Source
 */
class SignerAuthMethodOptions implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray() : array
    {
        return [
            ['value' => 'NONE', 'label' => __('None')],
            ['value' => 'PASSWORD', 'label' => __('Password')],
            ['value' => 'KBA', 'label' => __('KBA')],
            ['value' =>  'WEB_IDENTITY', 'label' => __('Web Identity')],
            ['value' =>  'ADOBE_SIGN', 'label' => __('Adobe Sign')],
            ['value' =>  'GOV_ID', 'label' => __('Government ID')]
        ];
    }
}

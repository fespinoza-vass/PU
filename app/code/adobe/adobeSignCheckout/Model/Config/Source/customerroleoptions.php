<?php

namespace adobe\adobeSignCheckout\Model\Config\Source;
use \Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Profile
 * @package Vendor\Package\Model\Config\Source
 */
class CustomerRoleOptions implements OptionSourceInterface
{
    /**
     * @return array
     */
     public function toOptionArray() : array
    {
        return [
            ['value' => 'SIGNER', 'label' => __('Signer')],
            ['value' => 'APPROVER', 'label' => __('Approver')],
            ['value' => 'ACCEPTOR', 'label' => __('Acceptor')],
            ['value' =>  'CERTIFIED_RECIPIENT', 'label' => __('Certified Recipient')],
        ];
    }
}

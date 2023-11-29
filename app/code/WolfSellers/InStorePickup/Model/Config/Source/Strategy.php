<?php
namespace WolfSellers\InStorePickup\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * @class Strategy
 */
class Strategy implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'distance_based', 'label' => __('Distance Based')],
            ['value' => 'stock_distance_based', 'label' => __('Stock Distance Based')],
            ['value' => 'custom_rules', 'label' => __('Custom Based')]
        ];
    }
}

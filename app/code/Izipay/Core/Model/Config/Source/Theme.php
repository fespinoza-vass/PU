<?php

namespace Izipay\Core\Model\Config\Source;

class Theme implements \Magento\Framework\Data\OptionSourceInterface
{
 	public function toOptionArray()
	{
		return [
			['value' => 'red', 'label' => __('red')],
			['value' => 'lightred', 'label' => __('lightred')],
			['value' => 'green', 'label' => __('green')],
			['value' => 'purple', 'label' => __('purple')],
			['value' => 'black', 'label' => __('black')],
			['value' => 'blue', 'label' => __('blue')],
			['value' => 'lightgreen', 'label' => __('lightgreen')],
		];
	}
}
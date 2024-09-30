<?php

namespace Izipay\Core\Model\Config\Source;

class Styleinput implements \Magento\Framework\Data\OptionSourceInterface
{
 	public function toOptionArray()
	{
		return [
			['value' => 'normal', 'label' => __('Normal')],
			['value' => 'compact', 'label' => __('Compacto')]
		];
	}
}
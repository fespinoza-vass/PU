<?php

namespace Izipay\Core\Model\Config\Source;

class Environment implements \Magento\Framework\Data\OptionSourceInterface
{
 	public function toOptionArray()
	{
		return [
			['value' => 'sandbox', 'label' => __('Integración (Test)')],
			['value' => 'production', 'label' => __('Producción')]
		];
	}
}
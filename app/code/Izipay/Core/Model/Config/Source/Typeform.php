<?php

namespace Izipay\Core\Model\Config\Source;

class Typeform implements \Magento\Framework\Data\OptionSourceInterface
{
 	public function toOptionArray()
	{
		return [
			['value' => 'pop-up', 'label' => __('Popup')],
			['value' => 'redirect', 'label' => __('Redirect')],
			['value' => 'embedded', 'label' => __('Embebido')]
		];
	}
}
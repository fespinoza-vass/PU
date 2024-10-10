<?php

namespace Izipay\Core\Model\Config\Source;

class Processtype implements \Magento\Framework\Data\OptionSourceInterface
{
 	public function toOptionArray()
	{
		return [
			['value' => 'AT', 'label' => __('Autorización')],
			['value' => 'PA', 'label' => __('Pre Autorización')]
		];
	}
}
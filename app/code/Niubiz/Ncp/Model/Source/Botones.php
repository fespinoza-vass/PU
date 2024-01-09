<?php

namespace Niubiz\Ncp\Model\Source;

class Botones implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            'DEFAULT' => 'DEFAULT',
            'SMALL' => 'SMALL',
            'MEDIUM' => 'MEDIUM',
            'LARGE' => 'LARGE'
        ];
    }
}
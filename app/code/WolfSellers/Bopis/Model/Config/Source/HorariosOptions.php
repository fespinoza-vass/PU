<?php

namespace WolfSellers\Bopis\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class HorariosOptions implements OptionSourceInterface
{
    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => '12_4',
                'label' => 'Horario 1',
            ],
            [
                'value' => '4_8',
                'label' => 'Horario 2',
            ]
        ];
    }
}

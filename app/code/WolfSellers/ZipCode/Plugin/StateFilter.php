<?php

namespace WolfSellers\ZipCode\Plugin;

use Magento\Directory\Model\ResourceModel\Region\Collection;

class StateFilter
{
    /**
     * The following regions are removed from the options. They do not have related address records
     * (CP, Towns, City)
     */
    protected $disallowed = [
        'El Callao',
        'Municipalidad Metropolitana de Lima',
    ];

    /**
     * @param Collection $subject
     * @param array $result
     * @return array
     */
    public function afterToOptionArray(Collection $subject, array $result): array
    {
        $results = array_filter($result, function ($option) {
            if (isset($option['label']))
                return !in_array($option['label'], $this->disallowed);
            return true;
        });

        return $results;
    }
}

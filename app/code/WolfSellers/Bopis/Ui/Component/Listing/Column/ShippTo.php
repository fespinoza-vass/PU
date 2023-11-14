<?php

namespace WolfSellers\Bopis\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

class ShippTo extends Column
{
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $shipping_method = $item['shipping_method'];
                $return = $item[$this->getData('name')];

                if ($shipping_method == 'instore_pickup'){
                    $return = $item['entregar_a'];
                }
                $item[$this->getData('name')] = $return;
            }
        }

        return $dataSource;
    }

}

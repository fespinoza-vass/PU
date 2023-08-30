<?php


namespace WolfSellers\Bopis\Ui\Component\Listing\Column;


use Magento\Ui\Component\Listing\Columns\Column;

class ShippingMethod extends Column
{
    const SHIPPING_INFORMATION = 'shipping_information';

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
                $shipping = $item[$this->getData('name')];
                if (isset($item[self::SHIPPING_INFORMATION])) {
                    $shipping = $item[self::SHIPPING_INFORMATION];
                }
                $item[$this->getData('name')] = $shipping;
            }
        }

        return $dataSource;
    }
}

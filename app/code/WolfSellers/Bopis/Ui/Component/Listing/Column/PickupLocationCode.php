<?php

namespace WolfSellers\Bopis\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\AbstractComponent;

class PickupLocationCode extends Column
{
    /** @var string  */
    const SHIPPING_INFORMATION = 'shipping_information';

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $sourceCode = $item[$this->getData('name')];
                $sourceName = '-';
                if ($sourceCode){
                    if (isset($item[self::SHIPPING_INFORMATION])) {
                        $sourceName = explode('-', $item[self::SHIPPING_INFORMATION]);
                        $sourceName = is_array($sourceName) ? end($sourceName) : '';
                    }
                }
                $item[$this->getData('name')] = $sourceName ?? $sourceCode;
            }
        }

        return $dataSource;
    }
}

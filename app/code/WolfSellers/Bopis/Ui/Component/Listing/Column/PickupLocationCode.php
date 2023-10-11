<?php

namespace WolfSellers\Bopis\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\AbstractComponent;
use WolfSellers\Backend\Helper\Data;

class PickupLocationCode extends Column
{
    /** @var string  */
    const SHIPPING_INFORMATION = 'shipping_information';

    /** @var Data  */
    protected Data $dataHelper;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Data $dataHelper
     * @param array $components
     * @param array $data
     */
    public function __construct(ContextInterface $context, UiComponentFactory $uiComponentFactory, Data $dataHelper, array $components = [], array $data = [])
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->dataHelper = $dataHelper;
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $sources = $this->dataHelper->getSourcesList();

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $sourceCode = $item[$this->getData('name')];
                $item[$this->getData('name')] = $sources[$sourceCode] ?? $sourceCode;
            }
        }

        return $dataSource;
    }
}

<?php

namespace WolfSellers\Bopis\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use WolfSellers\Bopis\Helper\RealStates;

class ShippingMethod extends Column
{
    /** @var RealStates  */
    protected $realStates;

    /** @var string  */
    const SHIPPING_INFORMATION = 'shipping_information';

    /** @var string  */
    const NEEDS_SUPPLY = 'needs_supply_instore';

    /**
     * @param RealStates $realStates
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        RealStates $realStates,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    )
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->realStates = $realStates;
    }

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
                $shippingTitle = $this->realStates->getShippingMethodTitle($shipping);

                $needsSupply = boolval($item[self::NEEDS_SUPPLY]);
                if ($needsSupply){
                    $shippingTitle = $shippingTitle . ' - desde almacen';
                }

                $item[$this->getData('name')] = $shippingTitle;
            }
        }

        return $dataSource;
    }
}

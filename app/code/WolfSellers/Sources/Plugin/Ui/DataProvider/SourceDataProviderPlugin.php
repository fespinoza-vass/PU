<?php

namespace WolfSellers\Sources\Plugin\Ui\DataProvider;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventoryAdminUi\Ui\DataProvider\SourceDataProvider;
use Magento\InventoryApi\Api\SourceRepositoryInterface;

class SourceDataProviderPlugin
{
    /**
     * @param SourceRepositoryInterface $_sourceRepository
     */
    public function __construct(
        protected SourceRepositoryInterface $_sourceRepository
    )
    {
    }

    /**
     * @param SourceDataProvider $subject
     * @param array $result
     * @return array
     * @throws NoSuchEntityException
     */
    public function afterGetData(SourceDataProvider $subject, array $result): array
    {
        if ('inventory_source_form_data_source' === $subject->getName()) {
            foreach ($result as $key => $value) {
                $item = $this->_sourceRepository->get($key);
                $result[$key]['bopis']['available_shipping_methods'] = $item->getData('available_shipping_methods');
                $result[$key]['bopis']['extension_attributes']['is_fastshipping_active'] = $item->getData('is_fastshipping_active');
                $result[$key]['bopis']['extension_attributes']['conductor'] = $item->getData('conductor');
                $result[$key]['bopis']['extension_attributes']['range_radius'] = $item->getData('range_radius');
                $result[$key]['bopis']['is_fastshipping_active'] = $item->getData('is_fastshipping_active');
                $result[$key]['bopis']['conductor'] = $item->getData('conductor');
                $result[$key]['bopis']['range_radius'] = $item->getData('range_radius');
                $result[$key]['general']['district'] = $item->getData('district');
            }
        }

        return $result;
    }
}

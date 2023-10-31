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
                $result[$key]['general']['district'] = $item->getData('district');
            }
        }

        return $result;
    }
}

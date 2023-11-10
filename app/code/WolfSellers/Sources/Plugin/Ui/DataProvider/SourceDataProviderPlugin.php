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
            if (isset($result['items'])) {
                foreach ($result['items'] as &$item) {
                    $sourceCode = $item['source_code'] ?? null;
                    if ($sourceCode) {
                        try {
                            $sourceItem = $this->_sourceRepository->get($sourceCode);
                            $item['bopis']['available_shipping_methods'] = $sourceItem->getData('available_shipping_methods');
                            $item['bopis']['extension_attributes']['is_fastshipping_active'] = $sourceItem->getData('is_fastshipping_active');
                            $item['bopis']['extension_attributes']['conductor'] = $sourceItem->getData('conductor');
                            $item['bopis']['extension_attributes']['range_radius'] = $sourceItem->getData('range_radius');
                            $item['bopis']['is_fastshipping_active'] = $sourceItem->getData('is_fastshipping_active');
                            $item['bopis']['conductor'] = $sourceItem->getData('conductor');
                            $item['bopis']['range_radius'] = $sourceItem->getData('range_radius');
                            $item['general']['district'] = $sourceItem->getData('district');
                        } catch (NoSuchEntityException $e) {
                            // @todo
                        }
                    }
                }
                unset($item);
            }
        }

        return $result;
    }
}

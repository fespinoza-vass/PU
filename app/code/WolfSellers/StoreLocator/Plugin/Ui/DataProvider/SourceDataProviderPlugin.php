<?php

namespace WolfSellers\StoreLocator\Plugin\Ui\DataProvider;

use Magento\Framework\Serialize\Serializer\Json;
use Magento\InventoryAdminUi\Ui\DataProvider\SourceDataProvider;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Psr\Log\LoggerInterface;

class SourceDataProviderPlugin
{
    /**
     * @param SourceRepositoryInterface $sourceRepository
     * @param LoggerInterface $logger
     * @param Json $_json
     */
    public function __construct(
        protected SourceRepositoryInterface $sourceRepository,
        protected LoggerInterface           $logger,
        protected Json                      $_json
    ){
    }

    /**
     * @param SourceDataProvider $subject
     * @param $result
     * @return mixed
     */
    public function afterGetData(SourceDataProvider $subject,$result) {
        if ('inventory_source_form_data_source' === $subject->getName()) {
            foreach ($result as $key => $value) {
                try {
                    $item = $this->sourceRepository->get($key);
                    $result[$key]['general']['store_code'] = $item->getData('store_code');
                    $result[$key]['opening_hours']['opening_hours'] = $this->_json->unserialize($item->getData('opening_hours'));
                } catch (\Throwable $e) {
                    $this->logger->error('Sources' . $e->getMessage());
                }
            }
        } elseif ('inventory_source_listing_data_source' === $subject->getName()) {
            if (array_key_exists('items', $result)) {
                foreach ($result['items'] as $key => $item) {
                    try {
                        $source = $this->sourceRepository->get($item['source_code']);
                        $result['items'][$key]['store_code'] = $source->getData('store_code');
                    } catch (\Throwable $e) {
                        $this->logger->error('Sources' . $e->getMessage());
                    }
                }
            }
        }

        return $result;
    }
}

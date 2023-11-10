<?php

namespace WolfSellers\Sources\Plugin\Ui\DataProvider;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventoryAdminUi\Ui\DataProvider\SourceDataProvider;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Psr\Log\LoggerInterface;

class SourceDataProviderPlugin
{
    protected $_logger;
    /**
     * @param SourceRepositoryInterface $_sourceRepository
     */
    public function __construct(
        protected SourceRepositoryInterface $_sourceRepository,
        LoggerInterface $logger
    )
    {
        $this->_logger = $logger;
    }

    /**
     * @param SourceDataProvider $subject
     * @param array $result
     * @return array
     * @throws NoSuchEntityException
     */
    public function afterGetData(SourceDataProvider $subject, array $result): array
    {
        try{
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
        }catch (\Exception $error){
            $this->_logger->error($error->getMessage());
        }

        return $result;
    }
}

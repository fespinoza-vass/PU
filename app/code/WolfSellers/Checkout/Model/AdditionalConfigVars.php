<?php

namespace WolfSellers\Checkout\Model;
use Magento\Directory\Model\Region;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;



class AdditionalConfigVars implements \Magento\Checkout\Model\ConfigProviderInterface
{
    /**
     * @param SourceRepositoryInterface $sourceRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param Region $regionModel
     * @param Json $json
     * @param TimezoneInterface $timezoneInterface
     */
    public function __construct(
        SourceRepositoryInterface $sourceRepository,
        SearchCriteriaBuilder     $searchCriteriaBuilder,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        Region                    $regionModel,
        Json                      $json,
        TimezoneInterface         $timezoneInterface
    )
    {
        $this->sourceRepository = $sourceRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->regionModel = $regionModel;
        $this->_json = $json;
        $this->timezoneInterface = $timezoneInterface;
    }


    /**
     * get sources shedules
     * @inheritDoc
     */
    public function getConfig()
    {
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria = $searchCriteriaBuilder->create();
        $sources = $this->sourceRepository->getList($searchCriteria)->getItems();
        $sourceData = [];

        foreach ($sources as $sourceItemName) {
            $pickup_location = (bool)$sourceItemName->getIsPickupLocationActive();
            if($pickup_location == 'true'){
                $sourceData[] = [
                    'source_code' => $sourceItemName->getSourceCode(),
                    'schedule' => $this->getOpeningHours($sourceItemName->getData('opening_hours'))
                ];
            }
        }

        $item = [
            'instore' => $sourceData
        ];
        return $item;
    }

    /**
     * get shedules
     * @param $hours
     * @return array|bool|float|int|mixed|string|null
     */

    private function getOpeningHours($hours)
    {
        if (is_null($hours)){
            return [];
        }
        return $this->_json->unserialize($hours);
    }

}

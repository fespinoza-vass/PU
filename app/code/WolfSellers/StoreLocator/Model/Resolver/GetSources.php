<?php
/**
 * WolfSellers_StoreLocator module
 */

namespace WolfSellers\StoreLocator\Model\Resolver;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Directory\Model\Region;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Class GetSources
 * @package WolfSellers\StoreLocator\Model\Resolver
 */
class GetSources implements ResolverInterface
{

    /** @var SourceRepositoryInterface */
    protected $sourceRepository;

    /** @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;

    /** @var Region */
    private $regionModel;

    /** @var Json */
    private $_json;


    /** @var string  */
    const URL = 'url';

    /**
     * @param SourceRepositoryInterface $sourceRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Region $regionModel
     * @param Json $json
     * @param TimezoneInterface $timezoneInterface
     */
    public function __construct(
        SourceRepositoryInterface $sourceRepository,
        SearchCriteriaBuilder     $searchCriteriaBuilder,
        Region                    $regionModel,
        Json                      $json,
        TimezoneInterface         $timezoneInterface
    )
    {
        $this->sourceRepository = $sourceRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->regionModel = $regionModel;
        $this->_json = $json;
        $this->timezoneInterface = $timezoneInterface;
    }

    /**
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array[]
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if ($args['store_code']) {
            $this->searchCriteriaBuilder->addFilter('store_code', $args['store_code']);
        }

        if (isset($args['source_code']) && !empty($args['source_code'])) {
            $this->searchCriteriaBuilder->addFilter('source_code', $args['source_code']);
        }

        $searchCriteria = $this->searchCriteriaBuilder->create();

        $searchCriteriaResult = $this->sourceRepository->getList($searchCriteria);
        $sources = $searchCriteriaResult->getItems();
        $sourceData = [];

        foreach ($sources as $source) {
            $sourceData[] = [
                'source_code' => $source->getSourceCode(),
                'name' => $source->getName(),
                'enabled' => (bool)$source->getEnabled(),
                'description' => $source->getDescription(),
                'latitude' => $source->getLatitude(),
                'longitude' => $source->getLongitude(),
                'pickup_location' => (bool)$source->getIsPickupLocationActive(),
                'contact_info' => [
                    'name' => $source->getContactName(),
                    'email' => $source->getEmail(),
                    'phone' => $source->getPhone(),
                    'fax' => $source->getFax()
                ],
                'address_data' => [
                    'country' => $source->getCountryId(),
                    'state' => $source->getRegion(),
                    'city' => $source->getCity(),
                    'street' => $source->getStreet(),
                    'postcode' => $source->getPostcode(),
                ],

                'schedule' => $this->getOpeningHours($source->getData('opening_hours'))
            ];
        }

        return [
            'sources' => $sourceData
        ];
    }

    /**
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


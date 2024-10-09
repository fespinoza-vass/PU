<?php

namespace Izipay\Core\Plugin;

use Izipay\Core\Ui\DataProvider\Notification\ListingDataProvider as NotificationDataProvider;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

class AddAttributesNToUiDataProvider
{
    /** @var AttributeRepositoryInterface */
    private $attributeRepository;

    /**
     * Constructor
     *
     * @param \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository
     */
    public function __construct(
        AttributeRepositoryInterface $attributeRepository
    ) {
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * Get Search Result after plugin
     *
     * @param \Izipay\Core\Ui\DataProvider\Notification\ListingDataProvider $subject
     * @param \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult $result
     * @return \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
     */
    public function afterGetSearchResult(NotificationDataProvider $subject, SearchResult $result)
    {
        if ($result->isLoaded()) {
            return $result;
        }

        return $result;
    }
}
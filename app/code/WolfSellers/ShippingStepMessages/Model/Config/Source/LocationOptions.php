<?php

namespace WolfSellers\ShippingStepMessages\Model\Config\Source;

use WolfSellers\ZipCode\Model\ResourceModel\ZipCode\CollectionFactory;

/**
 * @class LocationOptions
 */
class LocationOptions implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }


    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toOptionArray()
    {
        $collection = $this->collectionFactory->create();
        $localities = $collection->distinct(true)->addFieldToSelect('localidad')->setOrder('localidad', 'ASC');

        $options = [];
        foreach ($localities as $zipcode) {
            if ($locality = $zipcode->getData('localidad')) {
                $options[] = ['value' => $locality, 'label' => $locality];
            }
        }

        return $options;
    }
}

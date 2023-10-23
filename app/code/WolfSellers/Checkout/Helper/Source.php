<?php

namespace WolfSellers\Checkout\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Inventory\Model\ResourceModel\Source\CollectionFactory as SourceCollectionFactory;

/**
 *
 */
class Source extends AbstractHelper
{

    /**
     * @var SourceCollectionFactory
     */
    protected $_sourceCollectionFactorty;

    /**
     * @param Context $context
     * @param SourceCollectionFactory $sourceCollectionFactory
     */
    public function __construct(
        Context $context,
        SourceCollectionFactory $sourceCollectionFactory
    ) {
        $this->_sourceCollectionFactorty = $sourceCollectionFactory;
        parent::__construct($context);
    }

    /**
     * @return array
     */
    public function getDistrictSource(){
        $district = array();

        /** @var \Magento\Inventory\Model\ResourceModel\Source\Collection $collection */
        $collection = $this->_sourceCollectionFactorty->create()
            ->addFieldToSelect(['district'])
            ->addFieldToFilter('enabled', true)
            ->addFieldToFilter('is_pickup_location_active', true);
        $collection->getSelect()->group('district');

        if(count($collection->getItems())<=0){
            return $district;
        }

        foreach($collection->getItems() as $item){

            if(empty($item['district'])){
                continue;
            }

            $district[] = [
                'label' => $item['district'],
                'value' => $item['district']
            ];
        }
        return $district;
    }


}

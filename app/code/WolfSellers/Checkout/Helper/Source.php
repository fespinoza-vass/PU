<?php

namespace WolfSellers\Checkout\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Inventory\Model\ResourceModel\Source\CollectionFactory as SourceCollectionFactory;
use Magento\Framework\App\ResourceConnection;

/**
 *
 */
class Source extends AbstractHelper
{

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

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
        SourceCollectionFactory $sourceCollectionFactory,
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->_sourceCollectionFactorty = $sourceCollectionFactory;
        parent::__construct($context);
    }

    /**
     * @return array
     */
    public function getDistrictSource(){
        $district = [];
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('inventory_geoname');

        $qry = $connection->select()->from($tableName)
            ->where('is_district_lima = ?', 1)
            ->order("city ASC");

        $rows = $connection->fetchAssoc($qry);

        if(count($rows)<=0){
            return $district;
        }

        foreach($rows as $row){
            $district[] = [
                'label' => $row['city'],
                'value' => $row['city']
            ];
        }
        return $district;
    }
}

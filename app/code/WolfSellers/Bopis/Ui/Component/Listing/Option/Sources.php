<?php

namespace WolfSellers\Bopis\Ui\Component\Listing\Option;

use Magento\Backend\Model\Auth\Session as AuthSession;
use Magento\Framework\App\ResourceConnection;

class Sources implements \Magento\Framework\Option\ArrayInterface
{
    private ResourceConnection $resourceConnection;
    private AuthSession $authSession;

    public function __construct(
        AuthSession $authSession,
        ResourceConnection $resourceConnection
    ){
        $this->resourceConnection = $resourceConnection;
        $this->authSession = $authSession;
    }

    public function toOptionArray()
    {
        $storeCode = $this->authSession->getUser()->getData('source_code');
        $userType = $this->authSession->getUser()->getData('user_type');
        $websiteId = $this->authSession->getUser()->getData('website_id');
        $options = [['value' => '', 'label' => '']];
        $connection = $this->resourceConnection->getConnection();
        if ($userType == 2) {
            $sql = 'select is.source_code, is.name, issl.stock_id, issc.code, sw.website_id 
  from inventory_source `is`
  inner join inventory_source_stock_link issl on issl.source_code = is.source_code 
  inner join inventory_stock_sales_channel issc ON issc.stock_id = issl.stock_id
  inner join store_website sw on issc.code = sw.code and sw.website_id = \'' . $websiteId . '\';';
        } else {
            $sql = 'select source_code, name 
  from inventory_source `is` where source_code = \'' . $storeCode . '\';';
        }

        $results = $connection->fetchAssoc($sql);
        foreach ($results as $result) {
            $options[] = [
                'value' => $result["source_code"],
                'label' => $result["name"]
            ];
        }
        return $options;
    }
}
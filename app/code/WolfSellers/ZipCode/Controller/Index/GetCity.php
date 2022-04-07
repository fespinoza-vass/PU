<?php
/**
 * Created by.
 *
 * User: Juan Carlos Hdez <juanhernandez@wolfsellers.com>
 * Date: 2022-04-06
 * Time: 15:47
 */

declare(strict_types=1);

namespace WolfSellers\ZipCode\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Json\Helper\Data;

/**
 * Get Cities by region.
 */
class GetCity extends Action
{
    /** @var JsonFactory */
    private JsonFactory $resultJsonFactory;

    /** @var Data */
    private Data $jsonHelper;

    /** @var ResourceConnection */
    private ResourceConnection $resourceConnection;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param Data $jsonHelper
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        Data $jsonHelper,
        ResourceConnection $resourceConnection
    ) {
        parent::__construct($context);

        $this->resultJsonFactory = $resultJsonFactory;
        $this->jsonHelper = $jsonHelper;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        $regionId = $this->getRequest()->getParam('region_id');

        $connection = $this->resourceConnection->getConnection();
        $tableName = $connection->getTableName('wolfsellers_zipcode');
        $cols = [
            'value' => 'ciudad',
            'label' => 'ciudad',
        ];
        $select = $connection->select()
            ->from($tableName, $cols)
            ->where('region_id = ?', $regionId)
            ->order('ciudad ASC')
            ->distinct()
        ;

        $towns = $connection->fetchAll($select);

        return $this->resultJsonFactory->create()->setData($this->jsonHelper->jsonEncode($towns));
    }
}

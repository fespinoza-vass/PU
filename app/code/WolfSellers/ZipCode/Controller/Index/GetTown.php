<?php

namespace WolfSellers\ZipCode\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Json\Helper\Data;

/**
 * Get Town.
 */
class GetTown extends Action
{
    private JsonFactory $resultJsonFactory;
    private Data $jsonHelper;
    private ResourceConnection $resourceConnection;

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
     * Execute action based on request and return result
     *
     * Note: Request will be added as operation argument in future
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $regionId = $this->getRequest()->getParam('region_id');
        $city = $this->getRequest()->getParam('city');

        $connection = $this->resourceConnection->getConnection();
        $tableName = $connection->getTableName('wolfsellers_zipcode');
        $cols = [
            'value' => 'localidad',
            'label' => 'localidad',
            'postcode'
        ];
        $select = $connection->select()
            ->from($tableName, $cols)
            ->where('region_id = ?', $regionId)
            ->where('ciudad = ?', $city)
            ->order('localidad ASC')
        ;

        $towns = $connection->fetchAll($select);

        return $this->resultJsonFactory->create()->setData($this->jsonHelper->jsonEncode($towns));
    }
}

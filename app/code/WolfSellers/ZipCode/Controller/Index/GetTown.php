<?php

namespace WolfSellers\ZipCode\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Get Town.
 */
class GetTown implements HttpGetActionInterface
{
    /**
     * @var JsonFactory
     */
    private JsonFactory $resultJsonFactory;

    /**
     * @var ResourceConnection
     */
    private ResourceConnection $resourceConnection;

    /**
     * @var Json
     */
    private Json $json;

    /**
     * @var RequestInterface
     */
    protected RequestInterface $request;

    /**
     * @param JsonFactory $resultJsonFactory
     * @param Json $json
     * @param ResourceConnection $resourceConnection
     * @param RequestInterface $request
     */
    public function __construct(
        JsonFactory $resultJsonFactory,
        Json $json,
        ResourceConnection $resourceConnection,
        RequestInterface $request
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->json = $json;
        $this->resourceConnection = $resourceConnection;
        $this->request = $request;
    }

    /**
     * Execute action based on request and return result
     *
     * Note: Request will be added as operation argument in future
     *
     * @return ResultInterface|ResponseInterface
     */
    public function execute()
    {
        $regionId = $this->request->getParam('region_id');
        $city = $this->request->getParam('city');

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
            ->order('localidad ASC');

        $towns = $connection->fetchAll($select);

        return $this->resultJsonFactory->create()->setData($this->json->serialize($towns));
    }
}

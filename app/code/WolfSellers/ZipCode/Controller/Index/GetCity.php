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

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Get Cities by region.
 */
class GetCity implements HttpGetActionInterface
{
    /**
     * @var JsonFactory
     */
    private JsonFactory $resultJsonFactory;

    /**
     * @var Json
     */
    private Json $json;

    /**
     * @var ResourceConnection
     */
    private ResourceConnection $resourceConnection;

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
     * {@inheritDoc}
     */
    public function execute()
    {
        $regionId = $this->request->getParam('region_id');

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
            ->distinct();

        $towns = $connection->fetchAll($select);

        return $this->resultJsonFactory->create()->setData($this->json->serialize($towns));
    }
}

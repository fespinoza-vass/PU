<?php


namespace WolfSellers\ZipCode\Setup;

use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\App\ResourceConnection;

class InstallData implements InstallDataInterface
{

    protected $resource;

    protected $connection;

    private Reader $directoryList;

    public function __construct(
        ResourceConnection $resource,
        Reader $directoryList
    )
    {
        $this->connection = $resource->getConnection();
        $this->resource = $resource;
        $this->directoryList = $directoryList;
    }

    /**
     * {@inheritdoc}
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $filesPath = $this->directoryList->getModuleDir('', 'WolfSellers_ZipCode');
        $filesPath .= '/Files/peru.json';

        $data = json_decode(file_get_contents($filesPath), true);
        $this->insertMultiple('wolfsellers_zipcode', $data);
    }

    public function insertMultiple($table, $data)
    {
        $tableName = $this->resource->getTableName($table);
        $this->connection->insertMultiple($tableName, $data);
    }
}

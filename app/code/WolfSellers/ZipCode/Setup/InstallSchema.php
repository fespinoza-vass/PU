<?php


namespace WolfSellers\ZipCode\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();
        if (!$installer->tableExists('wolfsellers_zipcode')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('wolfsellers_zipcode')
            )
                ->addColumn(
                    'zip_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary'  => true,
                        'unsigned' => true,
                    ],
                    'Post ID'
                )
                ->addColumn(
                    'country_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    10,
                    ['nullable => false'],
                    'Country Id'
                )
                ->addColumn(
                    'region_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    50,
                    ['nullable => false'],
                    'Region Id'
                )
                ->addColumn(
                    'departamento',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [],
                    'departamento'
                )->addColumn(
                    'ciudad',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [],
                    'Ciudad'
                )->addColumn(
                    'localidad',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [],
                    'Localidad'
                )->addColumn(
                    'postcode',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    10,
                    [],
                    'Codigo Postal'
                );

            $installer->getConnection()->createTable($table);
        }
        $installer->endSetup();
    }
}

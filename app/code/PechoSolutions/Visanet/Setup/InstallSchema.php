<?php
namespace PechoSolutions\Visanet\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
 
    public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
 
        $columns = [
            'visanet_token' => [
              'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
              'length' => 50,
              'nullable' => false,
              'comment' => '.'
            ] 
        ];

        $connection = $installer->getConnection();
  
        foreach ($columns as $name => $definition) {
            $connection->addColumn( $installer->getTable('quote'), $name, $definition);
        }

        foreach ($columns as $name => $definition) {
            $connection->addColumn( $installer->getTable('sales_order'), $name, $definition);
        }
 
 
        $installer->endSetup();
 
    }
}

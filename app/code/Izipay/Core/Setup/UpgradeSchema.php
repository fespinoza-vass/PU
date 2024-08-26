<?php
namespace Izipay\Core\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            
			// Agregamos columnas extras en order
			$setup->getConnection()->addColumn(
				$setup->getTable('quote'),
				'izipay_alternative_payment_method',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'length' => 50,
					'nullable' => true,
					'default' => '',
					'comment' => 'Alternative Payment Method Izipay',
				]
			);

			$setup->getConnection()->addColumn(
				$setup->getTable('sales_order'),
				'izipay_alternative_payment_method',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'length' => 50,
					'nullable' => true,
					'default' => '',
					'comment' => 'Alternative Payment Method Izipay',
				]
			);

			$setup->getConnection()->addColumn(
				$setup->getTable('sales_order_grid'),
				'izipay_alternative_payment_method',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'length' => 50,
					'nullable' => true,
					'default' => '',
					'comment' => 'Alternative Payment Method Izipay',
				]
			);
        }

		if (version_compare($context->getVersion(), '1.0.4', '<')) {
			$setup->getConnection()->addIndex(
				$setup->getTable('izipay'),
				'IDX_ORDER_NUMBER_IZIPAY',
				'order_number'
			);
		}

        $setup->endSetup();
    }
}
<?php
namespace Izipay\Core\Setup;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
	{
		$installer = $setup;
		$installer->startSetup();

		if (!$installer->tableExists('izipay')) {
			$table = $installer->getConnection()->newTable($installer->getTable('izipay'))
				->addColumn(
					'id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					null,
					[
						'identity' => true,
						'nullable' => false,
						'primary'  => true,
						'unsigned' => true,
					],
					'ID'
				)
				->addColumn(
					'cart_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					50,
					['nullable' => false],
					'Cart ID'
				)
				->addColumn(
					'order_number',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					15,
					['nullable' => true],
					'Order Number'
				)
				->addColumn(
					'type_request',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					100,
					['nullable' => false],
					'Type Request'
				)
				->addColumn(
					'request',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					null,
					['nullable' => true],
					'Request'
				)
				->addColumn(
					'response',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					null,
					['nullable' => true],
					'Response'
				)
				->addColumn(
					'payment_status',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					255,
					['nullable' => true],
					'Payment status'
				)
				->addColumn(
					'created_at',
					\Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
					null,
					['nullable' => false],
					'Created at'
				)
				->addColumn(
					'updated_at',
					\Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
					null,
					['nullable' => false],
					'Updated at'
				)
			;

			$installer->getConnection()->createTable($table);
		}

		if (!$installer->tableExists('izipay_notification')) {
			$table = $installer->getConnection()->newTable($installer->getTable('izipay_notification'))
				->addColumn(
					'id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					null,
					[
						'identity' => true,
						'nullable' => false,
						'primary'  => true,
						'unsigned' => true,
					],
					'ID'
				)
				->addColumn(
					'response_data',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					null,
					['nullable' => false],
					'Response Data'
				)
				->addColumn(
					'status',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					255,
					['nullable' => false],
					'Status'
				)
				->addColumn(
					'created_at',
					\Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
					null,
					['nullable' => false],
					'Created At'
				)
			;

			$installer->getConnection()->createTable($table);
		}

		$installer->endSetup();
	}
}
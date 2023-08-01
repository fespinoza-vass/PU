<?php

namespace WolfSellers\OrderManagement\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @class OrderStatus Add states to Order
 */
class OrderStatus implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
    }
    /**
     * Add eav attributes
     */
    public function apply()
    {
        $installer = $this->moduleDataSetup;
        $installer->startSetup();
        $data[] = ['status' => 'confirmed_order', 'label' => 'Pedido confirmado'];
        $data[] = ['status' => 'prepared_order', 'label' => 'Pedido preparado'];
        $data[] = ['status' => 'order_on_the_way', 'label' => 'Pedido en camino'];
        $data[] = ['status' => 'order_ready_for_pick_up', 'label' => 'Pedido listo para recojo'];
        $data[] = ['status' => 'order_delivered', 'label' => 'Pedido entregado'];
        $this->moduleDataSetup->getConnection()->insertArray(
            $this->moduleDataSetup->getTable('sales_order_status'),
            ['status', 'label'],
            $data
        );
        $this->moduleDataSetup->getConnection()->insertArray(
            $this->moduleDataSetup->getTable('sales_order_status_state'),
            ['status', 'state', 'is_default','visible_on_front'],
            [
                ['confirmed_order','processing', '0', '1'],
                ['prepared_order','processing', '0', '1'],
                ['order_on_the_way','complete', '0', '1'],
                ['order_ready_for_pick_up','complete', '0', '1'],
                ['order_delivered', 'complete', '0', '1']
            ]
        );
        $installer->endSetup();
    }
    /**
     * Get dependencies
     */
    public static function getDependencies()
    {
        return [];
    }
    /**
     * Get Aliases
     */
    public function getAliases()
    {
        return [];
    }
}

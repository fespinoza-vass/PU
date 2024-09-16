<?php

namespace WolfSellers\Bopis\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

class RemoveApplyStorePickupProductAttribute implements DataPatchInterface, PatchRevertableInterface {
    const ATTRIBUTE_CODE = 'apply_store_pickup';

    /**
     * @var ModuleDataSetupInterface
     */
    private ModuleDataSetupInterface $_moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private EavSetupFactory $_eavSetupFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->_moduleDataSetup = $moduleDataSetup;
        $this->_eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies() {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases() {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function apply() {
        $productSetup = $this->_eavSetupFactory->create(['setup' => $this->_moduleDataSetup]);

        $productSetup->removeAttribute(Product::ENTITY, self::ATTRIBUTE_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function revert() {
    }
}

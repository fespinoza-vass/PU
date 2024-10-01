<?php

namespace Vass\CategoryAppImage\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    private $eavSetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            // Agregar o actualizar el atributo 'category_app_image'
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'category_app_image',
                [
                    'type' => 'varchar',
                    'label' => 'Category APP Image',
                    'input' => 'image',
                    'backend' => 'Magento\Catalog\Model\Category\Attribute\Backend\Image',
                    'required' => false,
                    'sort_order' => 100,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'Content',
                    'visible' => true,
                    'user_defined' => true,
                    'default' => '',
                    'note' => 'Maximum file size: 4 MB. Allowed file types: JPG, GIF, PNG.', // Mensaje de advertencia
                ]
            );
        }

        $setup->endSetup();
    }
}

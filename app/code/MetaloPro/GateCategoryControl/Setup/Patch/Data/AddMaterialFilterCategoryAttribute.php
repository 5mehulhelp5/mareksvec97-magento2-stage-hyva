<?php
namespace MetaloPro\GateCategoryControl\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Model\Category;
use Magento\Eav\Model\Config;

class AddMaterialFilterCategoryAttribute implements DataPatchInterface
{
    private $moduleDataSetup;
    private $eavSetupFactory;
    private $eavConfig;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory,
        Config $eavConfig
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
    }

    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $eavSetup->addAttribute(
            Category::ENTITY,
            'material_filter_value',
            [
                'type' => 'int',
                        'label' => 'Material Filter Value',
                        'input' => 'select',
                        'source' => \MetaloPro\GateCategoryControl\Model\Category\Attribute\Source\MaterialOptions::class,
                        'required' => false,
                        'sort_order' => 100,
                        'global' => ScopedAttributeInterface::SCOPE_STORE,
                        'group' => 'Display Settings',
                        'visible' => true,
                        'is_user_defined' => true
            ]
        );

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public static function getDependencies() { return []; }

    public function getAliases() { return []; }
}

<?php
namespace BigConnect\ProductConfigurator\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;

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

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            /** @var EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            // Dimensions to which you want to add unit_price
                $dimensions = ['length', 'width', 'height'];

                foreach ($dimensions as $dimension) {
                    // Add Unit Price attribute for each dimension
                    $eavSetup->addAttribute(
                        \Magento\Catalog\Model\Product::ENTITY,
                        $dimension . '_unit_price',
                        [
                            'type' => 'decimal',
                            'label' => ucfirst($dimension) . ' Unit Price',
                            'input' => 'price',
                            'backend' => 'Magento\Catalog\Model\Product\Attribute\Backend\Price',
                            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                            'visible' => true,
                            'required' => false,
                            'user_defined' => false,
                            'searchable' => false,
                            'filterable' => false,
                            'comparable' => false,
                            'visible_on_front' => false,
                            'used_in_product_listing' => true,
                            'unique' => false,
                            'apply_to' => 'simple,configurable,virtual,bundle,downloadable',
                            'system' => 1,
                            'group' => 'BigConnect Product Configurator',
                            'sort_order' => 10
                        ]
                    );
                }    

            // Add Yes/No attributes
            $enableAttributes = ['length', 'width', 'height'];
            foreach ($enableAttributes as $attribute) {
                $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY,
                    'calculation_' . $attribute . '_enable',
                    [
                        'type' => 'int',
                        'label' => 'Calculation ' . ucfirst($attribute) . ' Enable',
                        'input' => 'boolean',
                        'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                        'visible' => true,
                        'required' => false,
                        'user_defined' => false,
                        'default' => '0',
                        'searchable' => false,
                        'filterable' => false,
                        'comparable' => false,
                        'visible_on_front' => false,
                        'used_in_product_listing' => true,
                        'unique' => false,
                        'apply_to' => 'simple,configurable,virtual,bundle,downloadable',
                        'system' => 1,
                        'group' => 'BigConnect Product Configurator',
                        'sort_order' => 10
                    ]
                );
            }

            // Add Min, Max, Marker attributes
            $valueAttributes = ['length', 'width', 'height'];
            $attributeTypes = ['min', 'max', 'marker'];
            foreach ($valueAttributes as $attribute) {
                foreach ($attributeTypes as $type) {
                    $eavSetup->addAttribute(
                        \Magento\Catalog\Model\Product::ENTITY,
                        'calculation_' . $attribute . '_' . $type,
                        [
                            'type' => 'varchar',
                            'label' => 'Calculation ' . ucfirst($attribute) . ' ' . ucfirst($type),
                            'input' => 'text',
                            'backend' => '',
                            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                            'visible' => true,
                            'required' => false,
                            'user_defined' => false,
                            'searchable' => false,
                            'filterable' => false,
                            'comparable' => false,
                            'visible_on_front' => false,
                            'used_in_product_listing' => true,
                            'unique' => false,
                            'apply_to' => 'simple,configurable,virtual,bundle,downloadable',
                            'system' => 1,
                            'group' => 'BigConnect Product Configurator',
                            'sort_order' => 10
                        ]
                    );
                }
            }
        }

        $setup->endSetup();
    }
}

<?php
namespace BigConnect\CategoryDescription\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;

    /**
     * Constructor
     *
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        // Add bottom_description attribute
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'bottom_description',
            [
                'type' => 'text',
                'label' => 'Description',
                'input' => 'textarea',
                'required' => false,
                'sort_order' => 4,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'wysiwyg_enabled' => true,
                'is_html_allowed_on_front' => true,
                'group' => 'General Information',
            ]
        );

        // Add homepage_description attribute
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'homepage_description',
            [
                'type' => 'text',
                'label' => 'Homepage Description',
                'input' => 'textarea',
                'required' => false,
                'sort_order' => 5, // Adjust sort order if needed
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'wysiwyg_enabled' => true,
                'is_html_allowed_on_front' => true,
                'group' => 'General Information',
            ]
        );
        $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Category::ENTITY,
                    'homepage_title',
                    [
                        'type' => 'text',
                        'label' => 'Homepage Title',
                        'input' => 'text',  // text input type for a single line
                        'required' => false,
                        'sort_order' => 6,  // incremented the sort order
                        'global' => ScopedAttributeInterface::SCOPE_STORE,
                        'group' => 'General Information',
                    ]
                );
    }
}

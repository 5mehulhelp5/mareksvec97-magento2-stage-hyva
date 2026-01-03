<?php

namespace N1site\CategoryImage\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class InstallData
 * @package Tvojstyl\CategoryImage\Setup
 */

class InstallData implements InstallDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var \Magento\Catalog\Setup\CategorySetupFactory
     */
    private $categorySetupFactory;

    /**]
     * InstallData constructor.
     * @param EavSetupFactory $eavSetupFactory
     * @param \Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        \Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory
    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->categorySetupFactory = $categorySetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup = $this->categorySetupFactory->create(['setup' => $setup]);
        // $setup->addAttribute(
            // \Magento\Catalog\Model\Category::ENTITY, 'image2', [
                // 'type' => 'varchar',
                // 'label' => 'Category Image 2',
                // 'input' => 'image',
                // 'backend' => 'Magento\Catalog\Model\Category\Attribute\Backend\Image',
                // 'required' => false,
                // 'sort_order' => 9,
                // 'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                // 'group' => 'General Information',
            // ]
        // );
        $setup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY, 'image2', [
                'type' => 'varchar',
                'label' => 'Category Image 2',
                'input' => 'image',
                'backend' => 'Magento\Catalog\Model\Category\Attribute\Backend\Image',
                'required' => false,
                'sort_order' => 9,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'General Information',
            ]
        );
        $setup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY, 'icon_image', [
                'type' => 'varchar',
                'label' => 'Category Image 3',
                'input' => 'image',
                'backend' => 'Magento\Catalog\Model\Category\Attribute\Backend\Image',
                'required' => false,
                'sort_order' => 10,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'General Information',
            ]
        );
        $setup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY, 'icon', [
                'type' => 'text',
                'label' => 'Category Icon',
                'input' => 'textarea',
                'required' => false,
                'sort_order' => 11,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'General Information',
            ]
        );
    }
}
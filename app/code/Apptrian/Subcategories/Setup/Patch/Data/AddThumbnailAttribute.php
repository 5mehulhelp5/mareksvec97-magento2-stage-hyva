<?php
/**
 * @category  Apptrian
 * @package   Apptrian_Subcategories
 * @author    Apptrian
 * @copyright Copyright (c) Apptrian (http://www.apptrian.com)
 * @license   http://www.apptrian.com/license Proprietary Software License EULA
 */
 
namespace Apptrian\Subcategories\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Catalog\Setup\CategorySetupFactory;

/**
 * @codeCoverageIgnore
 */
class AddThumbnailAttribute implements DataPatchInterface
{
    /**
     * Magento module data setup setup interface.
     *
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * Magento EAV setup factory.
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
    
    /**
     * Magento Category setup factory.
     *
     * @var CategorySetupFactory
     */
    private $categorySetupFactory;
    
    /**
     * Constructor
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory,
        CategorySetupFactory $categorySetupFactory
    ) {
        $this->moduleDataSetup      = $moduleDataSetup;
        $this->eavSetupFactory      = $eavSetupFactory;
        $this->categorySetupFactory = $categorySetupFactory;
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $setup = $this->categorySetupFactory->create(['setup' => $this->moduleDataSetup]);
        $setup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'apptrian_thumbnail',
            [
                'type' => 'varchar',
                'label' => 'Category Thumbnail',
                'input' => 'image',
                'backend' => \Magento\Catalog\Model\Category\Attribute\Backend\Image::class,
                'required' => false,
                'sort_order' => 5,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'General Information',
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getVersion()
    {
        return '2.5.0';
    }
}

<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Setup\Patch\Schema;

use Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface;

class AddCategoryTemplateCategoryTable implements \Magento\Framework\Setup\Patch\SchemaPatchInterface
{
    const TABLE_CATEGORY_TEMPLATE_CATEGORY = 'ecwhim_seotemplates_category_template_category';
    const TABLE_CATALOG_CATEGORY_ENTITY    = 'catalog_category_entity';
    const TABLE_SEQUENCE_CATALOG_CATEGORY  = 'sequence_catalog_category';

    const COLUMN_CATEGORY_ID = 'category_id';

    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * AddCategoryTemplateCategoryTable constructor.
     *
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     */
    public function __construct(
        \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->productMetadata = $productMetadata;
    }

    /**
     * @return $this
     * @throws \Zend_Db_Exception
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();

        $connection = $this->moduleDataSetup->getConnection();
        $tableName  = $this->moduleDataSetup->getTable(self::TABLE_CATEGORY_TEMPLATE_CATEGORY);

        if (!$connection->isTableExists($tableName)) {
            $table = $connection->newTable($tableName);
            $table
                ->addColumn(
                    CategoryTemplateInterface::TEMPLATE_ID,
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    [
                        'nullable' => false,
                        'unsigned' => true
                    ],
                    'Template ID'
                )
                ->addColumn(
                    self::COLUMN_CATEGORY_ID,
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    [
                        'nullable' => false,
                        'unsigned' => true,
                        'default'  => 0
                    ],
                    'Category ID'
                )
                ->addIndex(
                    'PRIMARY',
                    [CategoryTemplateInterface::TEMPLATE_ID, self::COLUMN_CATEGORY_ID],
                    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_PRIMARY]
                )
                ->addIndex(
                    $connection->getIndexName(
                        $tableName,
                        [self::COLUMN_CATEGORY_ID],
                        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
                    ),
                    [self::COLUMN_CATEGORY_ID],
                    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX]
                )
                ->addForeignKey(
                    $connection->getForeignKeyName(
                        $tableName,
                        CategoryTemplateInterface::TEMPLATE_ID,
                        $this->moduleDataSetup->getTable(AddCategoryTemplateTable::TABLE_CATEGORY_TEMPLATE),
                        CategoryTemplateInterface::TEMPLATE_ID
                    ),
                    CategoryTemplateInterface::TEMPLATE_ID,
                    $this->moduleDataSetup->getTable(AddCategoryTemplateTable::TABLE_CATEGORY_TEMPLATE),
                    CategoryTemplateInterface::TEMPLATE_ID,
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->setComment('CategoryTemplate Category');

            if (strtolower($this->productMetadata->getEdition()) === 'enterprise') {
                $table->addForeignKey(
                    $connection->getForeignKeyName(
                        $tableName,
                        self::COLUMN_CATEGORY_ID,
                        $this->moduleDataSetup->getTable(self::TABLE_SEQUENCE_CATALOG_CATEGORY),
                        'sequence_value'
                    ),
                    self::COLUMN_CATEGORY_ID,
                    $this->moduleDataSetup->getTable(self::TABLE_SEQUENCE_CATALOG_CATEGORY),
                    'sequence_value',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                );
            } else {
                $table->addForeignKey(
                    $connection->getForeignKeyName(
                        $tableName,
                        self::COLUMN_CATEGORY_ID,
                        $this->moduleDataSetup->getTable(self::TABLE_CATALOG_CATEGORY_ENTITY),
                        'entity_id'
                    ),
                    self::COLUMN_CATEGORY_ID,
                    $this->moduleDataSetup->getTable(self::TABLE_CATALOG_CATEGORY_ENTITY),
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                );
            }

            $connection->createTable($table);
        }

        $this->moduleDataSetup->endSetup();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [
            AddCategoryTemplateTable::class
        ];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }
}

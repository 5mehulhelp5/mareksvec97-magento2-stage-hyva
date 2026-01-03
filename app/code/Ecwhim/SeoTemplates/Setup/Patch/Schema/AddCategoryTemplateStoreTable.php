<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Setup\Patch\Schema;

use Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface;

class AddCategoryTemplateStoreTable implements \Magento\Framework\Setup\Patch\SchemaPatchInterface
{
    const TABLE_CATEGORY_TEMPLATE_STORE = 'ecwhim_seotemplates_category_template_store';
    const TABLE_STORE                   = 'store';

    const COLUMN_STORE_ID = 'store_id';

    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * AddCategoryTemplateStoreTable constructor.
     *
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(\Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup)
    {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * @return $this
     * @throws \Zend_Db_Exception
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();

        $connection = $this->moduleDataSetup->getConnection();
        $tableName  = $this->moduleDataSetup->getTable(self::TABLE_CATEGORY_TEMPLATE_STORE);

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
                    self::COLUMN_STORE_ID,
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    5,
                    [
                        'nullable' => false,
                        'unsigned' => true,
                        'default'  => \Magento\Store\Model\Store::DEFAULT_STORE_ID
                    ],
                    'Store ID'
                )
                ->addIndex(
                    'PRIMARY',
                    [CategoryTemplateInterface::TEMPLATE_ID, self::COLUMN_STORE_ID],
                    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_PRIMARY]
                )
                ->addIndex(
                    $connection->getIndexName(
                        $tableName,
                        [self::COLUMN_STORE_ID],
                        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
                    ),
                    [self::COLUMN_STORE_ID],
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
                ->addForeignKey(
                    $connection->getForeignKeyName(
                        $tableName,
                        self::COLUMN_STORE_ID,
                        $this->moduleDataSetup->getTable(self::TABLE_STORE),
                        self::COLUMN_STORE_ID
                    ),
                    self::COLUMN_STORE_ID,
                    $this->moduleDataSetup->getTable(self::TABLE_STORE),
                    self::COLUMN_STORE_ID,
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->setComment('Category Template To Store Relations Table');

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

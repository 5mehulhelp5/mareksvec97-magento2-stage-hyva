<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Setup\Patch\Schema;

use Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface;

class AddCategoryTemplateTable implements \Magento\Framework\Setup\Patch\SchemaPatchInterface
{
    const TABLE_CATEGORY_TEMPLATE = 'ecwhim_seotemplates_category_template';

    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * AddCategoryTemplateTable constructor.
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
        $tableName  = $this->moduleDataSetup->getTable(self::TABLE_CATEGORY_TEMPLATE);

        if (!$connection->isTableExists($tableName)) {
            $table = $connection->newTable($tableName);
            $table
                ->addColumn(
                    CategoryTemplateInterface::TEMPLATE_ID,
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    [
                        'primary'  => true,
                        'identity' => true,
                        'nullable' => false,
                        'unsigned' => true
                    ],
                    'Template ID'
                )
                ->addColumn(
                    CategoryTemplateInterface::NAME,
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => false
                    ],
                    'Name'
                )
                ->addColumn(
                    CategoryTemplateInterface::IS_ACTIVE,
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    1,
                    [
                        'identity' => false,
                        'nullable' => false,
                        'unsigned' => false,
                        'default'  => 0
                    ],
                    'Is Active'
                )
                ->addColumn(
                    CategoryTemplateInterface::SCOPE,
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    7,
                    [
                        'nullable' => false
                    ],
                    'Scope'
                )
                ->addColumn(
                    CategoryTemplateInterface::TYPE,
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    64,
                    [
                        'nullable' => false
                    ],
                    'Type'
                )
                ->addColumn(
                    CategoryTemplateInterface::CONTENT,
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => false
                    ],
                    'Content'
                )
                ->addColumn(
                    CategoryTemplateInterface::APPLY_BY_CRON,
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    1,
                    [
                        'identity' => false,
                        'nullable' => false,
                        'unsigned' => false,
                        'default'  => 0
                    ],
                    'Apply by Cron'
                )
                ->addColumn(
                    CategoryTemplateInterface::PRIORITY,
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    [
                        'identity' => false,
                        'nullable' => false,
                        'unsigned' => true,
                        'default'  => 0
                    ],
                    'Priority'
                )
                ->addColumn(
                    CategoryTemplateInterface::APPLY_TO_ALL_CATEGORIES,
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    1,
                    [
                        'identity' => false,
                        'nullable' => false,
                        'unsigned' => false,
                        'default'  => 0
                    ],
                    'Apply to all Categories'
                )
                ->addColumn(
                    CategoryTemplateInterface::APPLICATION_TIME,
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    [
                        'nullable'  => true
                    ],
                    'Application Time'
                )
                ->setComment('Category Template Table');

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
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }
}

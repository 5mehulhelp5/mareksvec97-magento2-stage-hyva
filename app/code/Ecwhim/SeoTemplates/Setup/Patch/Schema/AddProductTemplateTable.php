<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Setup\Patch\Schema;

use Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterface;

class AddProductTemplateTable implements \Magento\Framework\Setup\Patch\SchemaPatchInterface
{
    const TABLE_PRODUCT_TEMPLATE = 'ecwhim_seotemplates_product_template';

    const COLUMN_CONDITIONS_SERIALIZED = 'conditions_serialized';

    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * AddProductTemplateTable constructor.
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
        $tableName  = $this->moduleDataSetup->getTable(self::TABLE_PRODUCT_TEMPLATE);

        if (!$connection->isTableExists($tableName)) {
            $table = $connection->newTable($tableName);
            $table
                ->addColumn(
                    ProductTemplateInterface::TEMPLATE_ID,
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
                    ProductTemplateInterface::NAME,
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => false
                    ],
                    'Name'
                )
                ->addColumn(
                    ProductTemplateInterface::IS_ACTIVE,
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
                    ProductTemplateInterface::SCOPE,
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    7,
                    [
                        'nullable' => false
                    ],
                    'Scope'
                )
                ->addColumn(
                    ProductTemplateInterface::TYPE,
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    64,
                    [
                        'nullable' => false
                    ],
                    'Type'
                )
                ->addColumn(
                    ProductTemplateInterface::CONTENT,
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => false
                    ],
                    'Content'
                )
                ->addColumn(
                    ProductTemplateInterface::APPLY_BY_CRON,
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
                    ProductTemplateInterface::PRIORITY,
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
                    ProductTemplateInterface::APPLICATION_TIME,
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    [
                        'nullable'  => true
                    ],
                    'Application Time'
                )
                ->addColumn(
                    self::COLUMN_CONDITIONS_SERIALIZED,
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    \Magento\Framework\Setup\Declaration\Schema\Dto\Factories\MediumText::DEFAULT_TEXT_LENGTH,
                    [
                        'nullable' => true
                    ],
                    'Conditions Serialized'
                )
                ->setComment('Product Template Table');

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

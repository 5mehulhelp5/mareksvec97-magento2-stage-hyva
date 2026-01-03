<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Setup\Patch\Schema;

use Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterface;

class AddProductTemplateProductTable implements \Magento\Framework\Setup\Patch\SchemaPatchInterface
{
    const TABLE_PRODUCT_TEMPLATE_PRODUCT = 'ecwhim_seotemplates_product_template_product';

    const COLUMN_STORE_ID   = 'store_id';
    const COLUMN_PRODUCT_ID = 'product_id';

    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * AddProductTemplateProductTable constructor.
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
        $tableName  = $this->moduleDataSetup->getTable(self::TABLE_PRODUCT_TEMPLATE_PRODUCT);

        if (!$connection->isTableExists($tableName)) {
            $table = $connection->newTable($tableName);
            $table
                ->addColumn(
                    'template_product_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    [
                        'primary'  => true,
                        'identity' => true,
                        'nullable' => false,
                        'unsigned' => true
                    ],
                    'Template Product ID'
                )
                ->addColumn(
                    ProductTemplateInterface::TEMPLATE_ID,
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    [
                        'nullable' => false,
                        'unsigned' => true
                    ],
                    'Template ID'
                )
                ->addColumn(
                    self::COLUMN_PRODUCT_ID,
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    [
                        'nullable' => false,
                        'unsigned' => true,
                        'default'  => 0
                    ],
                    'Product ID'
                )
                ->addColumn(
                    self::COLUMN_STORE_ID,
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    5,
                    [
                        'nullable' => false,
                        'unsigned' => true
                    ],
                    'Store ID'
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
                ->addIndex(
                    $connection->getIndexName(
                        $tableName,
                        [
                            ProductTemplateInterface::TEMPLATE_ID,
                            self::COLUMN_PRODUCT_ID,
                            self::COLUMN_STORE_ID
                        ],
                        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    [
                        ProductTemplateInterface::TEMPLATE_ID,
                        self::COLUMN_PRODUCT_ID,
                        self::COLUMN_STORE_ID
                    ],
                    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
                )
                ->addIndex(
                    $connection->getIndexName(
                        $tableName,
                        [ProductTemplateInterface::TEMPLATE_ID],
                        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
                    ),
                    [ProductTemplateInterface::TEMPLATE_ID],
                    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX]
                )
                ->addIndex(
                    $connection->getIndexName(
                        $tableName,
                        [self::COLUMN_PRODUCT_ID],
                        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
                    ),
                    [self::COLUMN_PRODUCT_ID],
                    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX]
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
                ->addIndex(
                    $connection->getIndexName(
                        $tableName,
                        [ProductTemplateInterface::SCOPE],
                        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
                    ),
                    [ProductTemplateInterface::SCOPE],
                    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX]
                )
                ->addIndex(
                    $connection->getIndexName(
                        $tableName,
                        [ProductTemplateInterface::TYPE],
                        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
                    ),
                    [ProductTemplateInterface::TYPE],
                    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX]
                )
                ->addIndex(
                    $connection->getIndexName(
                        $tableName,
                        [ProductTemplateInterface::PRIORITY],
                        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
                    ),
                    [ProductTemplateInterface::PRIORITY],
                    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX]
                )
                ->setComment('ProductTemplate Product');

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

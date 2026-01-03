<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model\Indexer;

class IndexerTableSwapper implements IndexerTableSwapperInterface
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resource;

    /**
     * @var string[]
     */
    private $tempTables = [];

    /**
     * IndexerTableSwapper constructor.
     *
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
    public function __construct(\Magento\Framework\App\ResourceConnection $resource)
    {
        $this->resource = $resource;
    }

    /**
     * @param string $originalTable
     * @return string
     * @throws \Exception
     */
    public function getWorkingTableName(string $originalTable): string
    {
        $originalTable = $this->resource->getTableName($originalTable);

        if (!array_key_exists($originalTable, $this->tempTables)) {
            $this->tempTables[$originalTable] = $this->createTemporaryTable($originalTable);
        }

        return $this->tempTables[$originalTable];
    }

    /**
     * @param array $originalTablesNames
     * @throws \Exception
     */
    public function swapIndexTables(array $originalTablesNames)
    {
        $toRename          = [];
        $toDrop            = [];
        $tempTablesRenamed = [];

        foreach ($originalTablesNames as $tableName) {
            $tableName        = $this->resource->getTableName($tableName);
            $tempTableName    = $this->getWorkingTableName($tableName);
            $tempOriginalName = $this->resource->getTableName(
                $tableName . $this->generateRandomSuffix()
            );

            $toRename[]          = [
                'oldName' => $tableName,
                'newName' => $tempOriginalName,
            ];
            $toRename[]          = [
                'oldName' => $tempTableName,
                'newName' => $tableName,
            ];
            $toDrop[]            = $tempOriginalName;
            $tempTablesRenamed[] = $tableName;
        }

        $this->resource->getConnection()->renameTablesBatch($toRename);

        foreach ($tempTablesRenamed as $tableName) {
            unset($this->tempTables[$tableName]);
        }

        foreach ($toDrop as $tableName) {
            $this->resource->getConnection()->dropTable($tableName);
        }
    }

    /**
     * @param string $originalTable
     * @return string
     * @throws \Exception
     */
    private function createTemporaryTable(string $originalTable): string
    {
        $temporaryTable = $this->resource->getTableName(
            $originalTable . '__temp' . $this->generateRandomSuffix()
        );

        $this->resource->getConnection()->query(
            sprintf('create table %s like %s', $temporaryTable, $this->resource->getTableName($originalTable))
        );

        return $temporaryTable;
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function generateRandomSuffix(): string
    {
        return bin2hex(random_bytes(4));
    }
}

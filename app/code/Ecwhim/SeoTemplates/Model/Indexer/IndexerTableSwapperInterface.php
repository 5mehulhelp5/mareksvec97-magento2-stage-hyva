<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model\Indexer;

/**
 * @api
 */
interface IndexerTableSwapperInterface
{
    /**
     * @param string $originalTable
     *
     * @return string
     */
    public function getWorkingTableName(string $originalTable): string;

    /**
     * @param string[] $originalTablesNames
     *
     * @return void
     */
    public function swapIndexTables(array $originalTablesNames);
}

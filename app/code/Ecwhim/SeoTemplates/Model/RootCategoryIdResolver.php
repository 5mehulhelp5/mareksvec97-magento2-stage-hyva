<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model;

class RootCategoryIdResolver
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resource;

    /**
     * @var array
     */
    private $rootCategoryIds = [];

    /**
     * RootCategoryIdResolver constructor.
     *
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
    public function __construct(\Magento\Framework\App\ResourceConnection $resource)
    {
        $this->resource = $resource;
    }

    /**
     * @param int $storeId
     * @return int
     */
    public function getRootCategoryId(int $storeId): int
    {
        if (isset($this->rootCategoryIds[$storeId])) {
            return $this->rootCategoryIds[$storeId];
        }

        $connection = $this->resource->getConnection();
        $select     = $connection->select();
        $select
            ->from(['s' => $this->resource->getTableName('store')], [])
            ->joinInner(
                ['g' => $this->resource->getTableName('store_group')],
                's.group_id = g.group_id',
                ['root_category_id']
            )
            ->where('s.store_id = ?', $storeId);

        $this->rootCategoryIds[$storeId] = (int)$connection->fetchOne($select);

        return $this->rootCategoryIds[$storeId];
    }
}

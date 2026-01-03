<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model\Indexer\ProductTemplate\Product;

use Ecwhim\SeoTemplates\Setup\Patch\Schema\AddProductTemplateProductTable;

class ProductTemplateIndexBuilder extends \Ecwhim\SeoTemplates\Model\Indexer\ProductTemplate\AbstractIndexBuilder
{
    /**
     * @param int $id
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function reindexById(int $id): void
    {
        $this->reindexByIds([$id]);
    }

    /**
     * @param array $ids
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function reindexByIds(array $ids): void
    {
        try {
            $this->doReindexByIds($ids);
        } catch (\Exception $e) {
            $this->logger->critical($e);

            throw new \Magento\Framework\Exception\LocalizedException(
                __("Ecwhim SEO Product Template indexing failed. See details in exception log.")
            );
        }
    }

    /**
     * @param array $productIds
     */
    public function cleanProductIndex(array $productIds): void
    {
        $this->resource->getConnection()->delete(
            $this->resource->getTableName(AddProductTemplateProductTable::TABLE_PRODUCT_TEMPLATE_PRODUCT),
            [AddProductTemplateProductTable::COLUMN_PRODUCT_ID . ' IN (?)' => $productIds]
        );
    }

    /**
     * @param array $ids
     */
    protected function doReindexByIds(array $ids): void
    {
        $this->cleanProductIndex($ids);

        foreach ($this->getAllTemplates() as $template) {
            $this->reindexTemplateProduct->execute($template, $this->batchSize, false, $ids);
        }
    }
}

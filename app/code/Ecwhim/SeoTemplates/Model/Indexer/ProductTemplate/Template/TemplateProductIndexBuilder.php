<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model\Indexer\ProductTemplate\Template;

use Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterface;
use Ecwhim\SeoTemplates\Setup\Patch\Schema\AddProductTemplateProductTable;

class TemplateProductIndexBuilder extends \Ecwhim\SeoTemplates\Model\Indexer\ProductTemplate\AbstractIndexBuilder
{
    /**
     * @param int $id
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function reindexById(int $id): void
    {
        try {
            $this->doReindexById($id);
        } catch (\Exception $e) {
            $this->logger->critical($e);

            throw new \Magento\Framework\Exception\LocalizedException(
                __("Ecwhim SEO Product Template indexing failed. See details in exception log.")
            );
        }
    }

    /**
     * @param array $templateIds
     */
    public function cleanTemplateIndex(array $templateIds): void
    {
        $this->resource->getConnection()->delete(
            $this->resource->getTableName(AddProductTemplateProductTable::TABLE_PRODUCT_TEMPLATE_PRODUCT),
            [ProductTemplateInterface::TEMPLATE_ID . ' IN (?)' => $templateIds]
        );
    }

    /**
     * @param int $id
     */
    protected function doReindexById(int $id): void
    {
        $this->cleanTemplateIndex([$id]);

        $template = $this->getActiveTemplateById($id);

        if ($template) {
            $this->reindexTemplateProduct->execute($template, $this->batchSize);
        }
    }

    /**
     * @param int $id
     * @return ProductTemplateInterface|null
     */
    protected function getActiveTemplateById(int $id): ?ProductTemplateInterface
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(ProductTemplateInterface::TEMPLATE_ID, $id);
        $collection->addFieldToFilter(
            ProductTemplateInterface::IS_ACTIVE,
            \Ecwhim\SeoTemplates\Model\Source\TemplateStatus::ACTIVE
        );

        if (count($collection)) {
            return $collection->getFirstItem();
        }

        return null;
    }
}

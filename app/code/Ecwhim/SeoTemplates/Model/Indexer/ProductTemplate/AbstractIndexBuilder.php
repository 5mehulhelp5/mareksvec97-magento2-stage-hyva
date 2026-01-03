<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model\Indexer\ProductTemplate;

use Ecwhim\SeoTemplates\Setup\Patch\Schema\AddProductTemplateProductTable;

abstract class AbstractIndexBuilder
{
    /**
     * @var \Ecwhim\SeoTemplates\Model\ResourceModel\ProductTemplate\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var ReindexTemplateProduct
     */
    protected $reindexTemplateProduct;

    /**
     * @var \Ecwhim\SeoTemplates\Model\Indexer\IndexerTableSwapperInterface
     */
    protected $tableSwapper;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var int
     */
    protected $batchSize;

    /**
     * AbstractIndexBuilder constructor.
     *
     * @param \Ecwhim\SeoTemplates\Model\ResourceModel\ProductTemplate\CollectionFactory $collectionFactory
     * @param ReindexTemplateProduct $reindexTemplateProduct
     * @param \Ecwhim\SeoTemplates\Model\Indexer\IndexerTableSwapperInterface $tableSwapper
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Psr\Log\LoggerInterface $logger
     * @param int $batchSize
     */
    public function __construct(
        \Ecwhim\SeoTemplates\Model\ResourceModel\ProductTemplate\CollectionFactory $collectionFactory,
        ReindexTemplateProduct $reindexTemplateProduct,
        \Ecwhim\SeoTemplates\Model\Indexer\IndexerTableSwapperInterface $tableSwapper,
        \Magento\Framework\App\ResourceConnection $resource,
        \Psr\Log\LoggerInterface $logger,
        int $batchSize = 1000
    ) {
        $this->collectionFactory      = $collectionFactory;
        $this->reindexTemplateProduct = $reindexTemplateProduct;
        $this->tableSwapper           = $tableSwapper;
        $this->resource               = $resource;
        $this->logger                 = $logger;
        $this->batchSize              = $batchSize;
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function reindexFull(): void
    {
        try {
            $this->doReindexFull();
        } catch (\Exception $e) {
            $this->logger->critical($e);

            throw new \Magento\Framework\Exception\LocalizedException(
                __("Ecwhim SEO Product Template indexing failed. See details in exception log.")
            );
        }
    }

    /**
     * @return void
     */
    protected function doReindexFull(): void
    {
        foreach ($this->getAllTemplates() as $template) {
            $this->reindexTemplateProduct->execute($template, $this->batchSize, true);
        }

        $this->tableSwapper->swapIndexTables(
            [$this->resource->getTableName(AddProductTemplateProductTable::TABLE_PRODUCT_TEMPLATE_PRODUCT)]
        );
    }

    /**
     * @return \Ecwhim\SeoTemplates\Model\ResourceModel\ProductTemplate\Collection
     */
    protected function getAllTemplates(): \Ecwhim\SeoTemplates\Model\ResourceModel\ProductTemplate\Collection
    {
        return $this->collectionFactory->create();
    }
}

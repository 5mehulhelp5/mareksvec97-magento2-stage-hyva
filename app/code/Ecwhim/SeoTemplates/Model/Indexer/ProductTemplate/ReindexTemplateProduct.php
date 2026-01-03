<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model\Indexer\ProductTemplate;

use Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterface;
use Ecwhim\SeoTemplates\Setup\Patch\Schema\AddProductTemplateProductTable;

class ReindexTemplateProduct
{
    /**
     * @var \Ecwhim\SeoTemplates\Model\ProductTemplate\TemplateProductsResolverInterface
     */
    protected $templateProductsResolver;

    /**
     * @var \Ecwhim\SeoTemplates\Model\TemplateStoreIdsResolver
     */
    protected $templateStoreIdsResolver;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Ecwhim\SeoTemplates\Model\Indexer\IndexerTableSwapperInterface
     */
    protected $tableSwapper;

    /**
     * ReindexTemplateProduct constructor.
     *
     * @param \Ecwhim\SeoTemplates\Model\ProductTemplate\TemplateProductsResolverInterface $templateProductsResolver
     * @param \Ecwhim\SeoTemplates\Model\TemplateStoreIdsResolver $templateStoreIdsResolver
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Ecwhim\SeoTemplates\Model\Indexer\IndexerTableSwapperInterface $tableSwapper
     */
    public function __construct(
        \Ecwhim\SeoTemplates\Model\ProductTemplate\TemplateProductsResolverInterface $templateProductsResolver,
        \Ecwhim\SeoTemplates\Model\TemplateStoreIdsResolver $templateStoreIdsResolver,
        \Magento\Framework\App\ResourceConnection $resource,
        \Ecwhim\SeoTemplates\Model\Indexer\IndexerTableSwapperInterface $tableSwapper
    ) {
        $this->templateProductsResolver = $templateProductsResolver;
        $this->templateStoreIdsResolver = $templateStoreIdsResolver;
        $this->resource                 = $resource;
        $this->tableSwapper             = $tableSwapper;
    }

    /**
     * @param ProductTemplateInterface|\Ecwhim\SeoTemplates\Model\ProductTemplate $template
     * @param int $batchSize
     * @param bool $useAdditionalTable
     * @param array|null $productIds
     * @return bool
     */
    public function execute(
        ProductTemplateInterface $template,
        int $batchSize,
        bool $useAdditionalTable = false,
        array $productIds = null
    ): bool {
        if (!$template->getIsActive() || empty($template->getStoreIds())) {
            return false;
        }

        $matchingProductIds = $this->templateProductsResolver->getMatchingProductIds($template, $productIds);

        if (empty($matchingProductIds)) {
            return true;
        }

        if ($useAdditionalTable) {
            $indexTable = $this->resource->getTableName(
                $this->tableSwapper->getWorkingTableName(AddProductTemplateProductTable::TABLE_PRODUCT_TEMPLATE_PRODUCT)
            );
        } else {
            $indexTable = $this->resource->getTableName(AddProductTemplateProductTable::TABLE_PRODUCT_TEMPLATE_PRODUCT);
        }

        $storeIds   = $this->templateStoreIdsResolver->getStoreIds($template);
        $connection = $this->resource->getConnection();
        $templateId = $template->getId();
        $scope      = $template->getScope();
        $type       = $template->getType();
        $priority   = $template->getPriority();
        $rows       = [];
        $rowCount   = 0;

        foreach ($storeIds as $storeId) {
            foreach ($matchingProductIds as $productId => $validationByStore) {
                if (empty($validationByStore[$storeId])) {
                    continue;
                }

                $rows[] = [
                    ProductTemplateInterface::TEMPLATE_ID             => $templateId,
                    AddProductTemplateProductTable::COLUMN_PRODUCT_ID => $productId,
                    AddProductTemplateProductTable::COLUMN_STORE_ID   => $storeId,
                    ProductTemplateInterface::SCOPE                   => $scope,
                    ProductTemplateInterface::TYPE                    => $type,
                    ProductTemplateInterface::PRIORITY                => $priority
                ];
                $rowCount++;

                if ($rowCount == $batchSize) {
                    $connection->insertOnDuplicate($indexTable, $rows);

                    $rows     = [];
                    $rowCount = 0;
                }
            }
        }

        if (!empty($rows)) {
            $connection->insertOnDuplicate($indexTable, $rows);
        }

        return true;
    }
}

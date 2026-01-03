<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Plugin\Indexer\ProductTemplate\Product;

use Ecwhim\SeoTemplates\Model\Indexer\ProductTemplate\Product\ProductTemplateProcessor;

class ReindexAfterCategorySavePlugin
{
    /**
     * @var ProductTemplateProcessor
     */
    protected $productTemplateProcessor;

    /**
     * ReindexAfterCategorySavePlugin constructor.
     *
     * @param ProductTemplateProcessor $productTemplateProcessor
     */
    public function __construct(ProductTemplateProcessor $productTemplateProcessor)
    {
        $this->productTemplateProcessor = $productTemplateProcessor;
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Category $subject
     * @param \Magento\Catalog\Model\ResourceModel\Category $result
     * @param \Magento\Framework\Model\AbstractModel $category
     * @return \Magento\Catalog\Model\ResourceModel\Category
     */
    public function afterSave(
        \Magento\Catalog\Model\ResourceModel\Category $subject,
        \Magento\Catalog\Model\ResourceModel\Category $result,
        \Magento\Framework\Model\AbstractModel $category
    ): \Magento\Catalog\Model\ResourceModel\Category {
        $productIds = $category->getChangedProductIds();

        if (!empty($productIds)) {
            $this->productTemplateProcessor->reindexList($productIds);
        }

        return $result;
    }
}

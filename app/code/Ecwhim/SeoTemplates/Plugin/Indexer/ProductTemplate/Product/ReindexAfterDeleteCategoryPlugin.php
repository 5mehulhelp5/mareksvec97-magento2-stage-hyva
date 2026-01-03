<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Plugin\Indexer\ProductTemplate\Product;

use Ecwhim\SeoTemplates\Model\Indexer\ProductTemplate\Product\ProductTemplateProcessor;

class ReindexAfterDeleteCategoryPlugin
{
    /**
     * @var ProductTemplateProcessor
     */
    protected $productTemplateProcessor;

    /**
     * ReindexAfterDeleteCategoryPlugin constructor.
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
     * @return \Magento\Catalog\Model\ResourceModel\Category
     */
    public function afterDelete(
        \Magento\Catalog\Model\ResourceModel\Category $subject,
        \Magento\Catalog\Model\ResourceModel\Category $result
    ): \Magento\Catalog\Model\ResourceModel\Category {
        $this->productTemplateProcessor->markIndexerAsInvalid();

        return $result;
    }
}

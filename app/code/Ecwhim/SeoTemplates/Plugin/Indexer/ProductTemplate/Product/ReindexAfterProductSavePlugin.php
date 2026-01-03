<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Plugin\Indexer\ProductTemplate\Product;

use Ecwhim\SeoTemplates\Model\Indexer\ProductTemplate\Product\ProductTemplateProcessor;

class ReindexAfterProductSavePlugin
{
    /**
     * @var ProductTemplateProcessor
     */
    protected $productTemplateProcessor;

    /**
     * ReindexAfterProductSavePlugin constructor.
     *
     * @param ProductTemplateProcessor $productTemplateProcessor
     */
    public function __construct(ProductTemplateProcessor $productTemplateProcessor)
    {
        $this->productTemplateProcessor = $productTemplateProcessor;
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product $subject
     * @param \Magento\Catalog\Model\ResourceModel\Product $result
     * @param \Magento\Framework\Model\AbstractModel $product
     * @return \Magento\Catalog\Model\ResourceModel\Product
     */
    public function afterSave(
        \Magento\Catalog\Model\ResourceModel\Product $subject,
        \Magento\Catalog\Model\ResourceModel\Product $result,
        \Magento\Framework\Model\AbstractModel $product
    ): \Magento\Catalog\Model\ResourceModel\Product {
        $this->productTemplateProcessor->reindexRow($product->getId());

        return $result;
    }
}

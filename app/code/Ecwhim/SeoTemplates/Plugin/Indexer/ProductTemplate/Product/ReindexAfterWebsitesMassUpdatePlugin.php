<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Plugin\Indexer\ProductTemplate\Product;

use Ecwhim\SeoTemplates\Model\Indexer\ProductTemplate\Product\ProductTemplateProcessor;

class ReindexAfterWebsitesMassUpdatePlugin
{
    /**
     * @var ProductTemplateProcessor
     */
    protected $productTemplateProcessor;

    /**
     * ReindexAfterWebsitesMassUpdatePlugin constructor.
     *
     * @param ProductTemplateProcessor $productTemplateProcessor
     */
    public function __construct(ProductTemplateProcessor $productTemplateProcessor)
    {
        $this->productTemplateProcessor = $productTemplateProcessor;
    }

    /**
     * @param \Magento\Catalog\Model\Product\Action $subject
     * @param null $result
     * @param array $productIds
     */
    public function afterUpdateWebsites(
        \Magento\Catalog\Model\Product\Action $subject,
        $result,
        array $productIds
    ): void {
        $this->productTemplateProcessor->reindexList(array_unique($productIds));
    }
}

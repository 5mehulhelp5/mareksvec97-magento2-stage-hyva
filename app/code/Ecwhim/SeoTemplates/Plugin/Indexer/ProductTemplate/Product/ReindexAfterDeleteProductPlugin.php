<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Plugin\Indexer\ProductTemplate\Product;

use Ecwhim\SeoTemplates\Model\Indexer\ProductTemplate\Product\ProductTemplateIndexBuilder;

class ReindexAfterDeleteProductPlugin
{
    /**
     * @var ProductTemplateIndexBuilder
     */
    private $indexBuilder;

    /**
     * ReindexAfterDeleteProductPlugin constructor.
     *
     * @param ProductTemplateIndexBuilder $indexBuilder
     */
    public function __construct(ProductTemplateIndexBuilder $indexBuilder)
    {
        $this->indexBuilder = $indexBuilder;
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product $subject
     * @param \Magento\Catalog\Model\ResourceModel\Product $result
     * @param \Magento\Framework\Model\AbstractModel $product
     * @return \Magento\Catalog\Model\ResourceModel\Product
     */
    public function afterDelete(
        \Magento\Catalog\Model\ResourceModel\Product $subject,
        \Magento\Catalog\Model\ResourceModel\Product $result,
        \Magento\Framework\Model\AbstractModel $product
    ): \Magento\Catalog\Model\ResourceModel\Product {
        $this->indexBuilder->cleanProductIndex([$product->getId()]);

        return $result;
    }
}

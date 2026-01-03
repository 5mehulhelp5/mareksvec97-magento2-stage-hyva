<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model\Indexer\ProductTemplate\Product;

class ProductTemplateIndexer extends \Ecwhim\SeoTemplates\Model\Indexer\ProductTemplate\AbstractIndexer
{
    /**
     * @var ProductTemplateIndexBuilder
     */
    protected $indexBuilder;

    /**
     * ProductTemplateIndexer constructor.
     *
     * @param ProductTemplateIndexBuilder $indexBuilder
     */
    public function __construct(ProductTemplateIndexBuilder $indexBuilder)
    {
        parent::__construct($indexBuilder);
    }

    /**
     * @param int[] $ids
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function executeList(array $ids)
    {
        if (empty($ids)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Could not rebuild index for empty products array.')
            );
        }

        $this->indexBuilder->reindexByIds($ids);
    }

    /**
     * @param int $id
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function executeRow($id)
    {
        if (empty($id)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('We can\'t rebuild the index for an undefined product.')
            );
        }

        $this->indexBuilder->reindexById((int)$id);
    }
}

<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model\Indexer\ProductTemplate\Template;

class TemplateProductIndexer extends \Ecwhim\SeoTemplates\Model\Indexer\ProductTemplate\AbstractIndexer
{
    /**
     * @var TemplateProductIndexBuilder
     */
    protected $indexBuilder;

    /**
     * TemplateProductIndexer constructor.
     *
     * @param TemplateProductIndexBuilder $indexBuilder
     */
    public function __construct(TemplateProductIndexBuilder $indexBuilder)
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
                __('Could not rebuild index for empty templates array.')
            );
        }

        $this->indexBuilder->reindexFull();
    }

    /**
     * @param int $id
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function executeRow($id)
    {
        if (empty($id)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('We can\'t rebuild the index for an undefined template.')
            );
        }

        $this->indexBuilder->reindexById((int)$id);
    }
}

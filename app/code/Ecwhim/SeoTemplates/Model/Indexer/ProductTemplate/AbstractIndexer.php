<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model\Indexer\ProductTemplate;

use Magento\Framework\Indexer\ActionInterface as IndexerActionInterface;

abstract class AbstractIndexer implements IndexerActionInterface, \Magento\Framework\Mview\ActionInterface
{
    const SHARED_INDEX = 'ecwhim_seotemplates_product_template';

    /**
     * @var AbstractIndexBuilder
     */
    protected $indexBuilder;

    /**
     * AbstractIndexer constructor.
     *
     * @param AbstractIndexBuilder $indexBuilder
     */
    public function __construct(AbstractIndexBuilder $indexBuilder)
    {
        $this->indexBuilder = $indexBuilder;
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function executeFull()
    {
        $this->indexBuilder->reindexFull();
    }

    /**
     * @inheritDoc
     */
    public function execute($ids)
    {
        $this->executeList($ids);
    }
}

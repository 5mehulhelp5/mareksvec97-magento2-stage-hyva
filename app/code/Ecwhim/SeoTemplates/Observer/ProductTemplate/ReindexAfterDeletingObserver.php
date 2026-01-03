<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Observer\ProductTemplate;

use Ecwhim\SeoTemplates\Model\Indexer\ProductTemplate\Template\TemplateProductIndexBuilder;

class ReindexAfterDeletingObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var TemplateProductIndexBuilder
     */
    protected $indexBuilder;

    /**
     * ReindexAfterDeletingObserver constructor.
     *
     * @param TemplateProductIndexBuilder $indexBuilder
     */
    public function __construct(TemplateProductIndexBuilder $indexBuilder)
    {
        $this->indexBuilder = $indexBuilder;
    }

    /**
     * @inheritDoc
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Ecwhim\SeoTemplates\Model\ProductTemplate $template */
        $template = $observer->getEvent()->getEntity();

        $this->indexBuilder->cleanTemplateIndex([$template->getTemplateId()]);
    }
}

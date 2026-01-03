<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Observer\ProductTemplate;

use Ecwhim\SeoTemplates\Model\Indexer\ProductTemplate\Template\TemplateProductProcessor;

class ReindexAfterSavingObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Ecwhim\SeoTemplates\Model\ResourceModel\ProductTemplate
     */
    private $templateResource;

    /**
     * @var TemplateProductProcessor
     */
    private $templateProductProcessor;

    /**
     * ReindexAfterSavingObserver constructor.
     *
     * @param \Ecwhim\SeoTemplates\Model\ResourceModel\ProductTemplate $templateResource
     * @param TemplateProductProcessor $templateProductProcessor
     */
    public function __construct(
        \Ecwhim\SeoTemplates\Model\ResourceModel\ProductTemplate $templateResource,
        TemplateProductProcessor $templateProductProcessor
    ) {
        $this->templateResource         = $templateResource;
        $this->templateProductProcessor = $templateProductProcessor;
    }

    /**
     * @inheritDoc
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->templateProductProcessor->isIndexerScheduled()) {
            $this->templateProductProcessor->markIndexerAsInvalid();
        } else {
            /** @var \Ecwhim\SeoTemplates\Model\ProductTemplate $template */
            $template   = $observer->getEvent()->getEntity();
            $templateId = $template->getTemplateId();

            $this->templateResource->addCommitCallback(
                function () use ($templateId) {
                    $this->templateProductProcessor->reindexRow($templateId);
                }
            );
        }
    }
}

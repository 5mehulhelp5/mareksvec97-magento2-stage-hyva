<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Plugin\Indexer\ProductTemplate\Template;

use Ecwhim\SeoTemplates\Model\Indexer\ProductTemplate\Template\TemplateProductProcessor;

class ReindexAfterDeleteWebsitePlugin
{
    /**
     * @var TemplateProductProcessor
     */
    protected $templateProductProcessor;

    /**
     * ReindexAfterDeleteWebsitePlugin constructor.
     *
     * @param TemplateProductProcessor $templateProductProcessor
     */
    public function __construct(TemplateProductProcessor $templateProductProcessor)
    {
        $this->templateProductProcessor = $templateProductProcessor;
    }

    /**
     * @param \Magento\Store\Model\ResourceModel\Website $subject
     * @param \Magento\Store\Model\ResourceModel\Website $result
     * @return \Magento\Store\Model\ResourceModel\Website
     */
    public function afterDelete(
        \Magento\Store\Model\ResourceModel\Website $subject,
        \Magento\Store\Model\ResourceModel\Website $result
    ): \Magento\Store\Model\ResourceModel\Website {
        $this->templateProductProcessor->markIndexerAsInvalid();

        return $result;
    }
}

<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Plugin\Indexer\ProductTemplate\Template;

use Ecwhim\SeoTemplates\Model\Indexer\ProductTemplate\Template\TemplateProductProcessor;

class ReindexAfterDeleteStorePlugin
{
    /**
     * @var TemplateProductProcessor
     */
    protected $templateProductProcessor;

    /**
     * ReindexAfterDeleteStorePlugin constructor.
     *
     * @param TemplateProductProcessor $templateProductProcessor
     */
    public function __construct(TemplateProductProcessor $templateProductProcessor)
    {
        $this->templateProductProcessor = $templateProductProcessor;
    }

    /**
     * @param \Magento\Store\Model\ResourceModel\Store $subject
     * @param \Magento\Store\Model\ResourceModel\Store $result
     * @return \Magento\Store\Model\ResourceModel\Store
     */
    public function afterDelete(
        \Magento\Store\Model\ResourceModel\Store $subject,
        \Magento\Store\Model\ResourceModel\Store $result
    ): \Magento\Store\Model\ResourceModel\Store {
        $this->templateProductProcessor->markIndexerAsInvalid();

        return $result;
    }
}

<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Plugin\Indexer\ProductTemplate\Template;

use Ecwhim\SeoTemplates\Model\Indexer\ProductTemplate\Template\TemplateProductProcessor;

class ReindexAfterStoreSavePlugin
{
    /**
     * @var TemplateProductProcessor
     */
    protected $templateProductProcessor;

    /**
     * @var bool
     */
    private $needInvalidation;

    /**
     * ReindexAfterStoreSavePlugin constructor.
     *
     * @param TemplateProductProcessor $templateProductProcessor
     */
    public function __construct(TemplateProductProcessor $templateProductProcessor)
    {
        $this->templateProductProcessor = $templateProductProcessor;
    }

    /**
     * @param \Magento\Store\Model\ResourceModel\Store $subject
     * @param \Magento\Framework\Model\AbstractModel $store
     */
    public function beforeSave(
        \Magento\Store\Model\ResourceModel\Store $subject,
        \Magento\Framework\Model\AbstractModel $store
    ) {
        $this->needInvalidation = $store->isObjectNew();
    }

    /**
     * @param \Magento\Store\Model\ResourceModel\Store $subject
     * @param \Magento\Store\Model\ResourceModel\Store $result
     * @return \Magento\Store\Model\ResourceModel\Store
     */
    public function afterSave(
        \Magento\Store\Model\ResourceModel\Store $subject,
        \Magento\Store\Model\ResourceModel\Store $result
    ): \Magento\Store\Model\ResourceModel\Store {
        if ($this->needInvalidation) {
            $this->templateProductProcessor->markIndexerAsInvalid();
        }

        return $result;
    }
}

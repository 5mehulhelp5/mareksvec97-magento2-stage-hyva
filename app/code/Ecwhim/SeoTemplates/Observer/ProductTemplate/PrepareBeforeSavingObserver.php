<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Observer\ProductTemplate;

class PrepareBeforeSavingObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $jsonSerializer;

    /**
     * PrepareBeforeSavingObserver constructor.
     *
     * @param \Magento\Framework\Serialize\Serializer\Json $jsonSerializer
     */
    public function __construct(\Magento\Framework\Serialize\Serializer\Json $jsonSerializer)
    {
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * @inheritDoc
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Ecwhim\SeoTemplates\Model\ProductTemplate $template */
        $template = $observer->getEvent()->getEntity();

        $this->prepareStoreIds($template);

        if ($template->getConditions()) {
            $template->setConditionsSerialized($this->jsonSerializer->serialize($template->getConditions()->asArray()));
        }
    }

    /**
     * @param \Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterface $template
     */
    private function prepareStoreIds(\Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterface $template): void
    {
        if ($template->getScope() === \Ecwhim\SeoTemplates\Model\Source\Scope::SCOPE_GLOBAL) {
            $template->setStoreIds([\Magento\Store\Model\Store::DEFAULT_STORE_ID]);
        } elseif ($template->getScope() === \Ecwhim\SeoTemplates\Model\Source\Scope::SCOPE_STORE
            && count($template->getStoreIds()) > 1
            && in_array(\Magento\Store\Model\Store::DEFAULT_STORE_ID, $template->getStoreIds())
        ) {
            $template->setStoreIds([\Magento\Store\Model\Store::DEFAULT_STORE_ID]);
        }
    }
}

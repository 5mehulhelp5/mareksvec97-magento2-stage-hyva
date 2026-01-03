<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Observer\CategoryTemplate;

class PrepareBeforeSavingObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @inheritDoc
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Ecwhim\SeoTemplates\Model\CategoryTemplate $template */
        $template = $observer->getEvent()->getEntity();

        $this->prepareStoreIds($template);
    }

    /**
     * @param \Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface $template
     */
    private function prepareStoreIds(\Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface $template): void
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

<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Api\Data;

/**
 * @api
 */
interface ProductTemplateInterface extends TemplateInterface
{
    /**
     * @return \Magento\CatalogRule\Api\Data\ConditionInterface|null
     */
    public function getTemplateCondition(): ?\Magento\CatalogRule\Api\Data\ConditionInterface;

    /**
     * @param \Magento\CatalogRule\Api\Data\ConditionInterface $condition
     * @return void
     */
    public function setTemplateCondition(\Magento\CatalogRule\Api\Data\ConditionInterface $condition): void;

    /**
     * @return \Ecwhim\SeoTemplates\Api\Data\ProductTemplateExtensionInterface|null
     */
    public function getExtensionAttributes(): ?\Ecwhim\SeoTemplates\Api\Data\ProductTemplateExtensionInterface;

    /**
     * @param \Ecwhim\SeoTemplates\Api\Data\ProductTemplateExtensionInterface $extensionAttributes
     * @return void
     */
    public function setExtensionAttributes(
        \Ecwhim\SeoTemplates\Api\Data\ProductTemplateExtensionInterface $extensionAttributes
    ): void;
}

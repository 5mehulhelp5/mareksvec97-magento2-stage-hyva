<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Api\Data;

/**
 * @api
 */
interface CategoryTemplateInterface extends TemplateInterface
{
    const APPLY_TO_ALL_CATEGORIES = 'apply_to_all_categories';

    /**
     * @return int
     */
    public function getApplyToAllCategories(): int;

    /**
     * @param int $applyToAllCategories
     * @return void
     */
    public function setApplyToAllCategories(int $applyToAllCategories): void;

    /**
     * @return \Ecwhim\SeoTemplates\Api\Data\CategoryTemplateExtensionInterface|null
     */
    public function getExtensionAttributes(): ?\Ecwhim\SeoTemplates\Api\Data\CategoryTemplateExtensionInterface;

    /**
     * @param \Ecwhim\SeoTemplates\Api\Data\CategoryTemplateExtensionInterface $extensionAttributes
     * @return void
     */
    public function setExtensionAttributes(
        \Ecwhim\SeoTemplates\Api\Data\CategoryTemplateExtensionInterface $extensionAttributes
    ): void;
}

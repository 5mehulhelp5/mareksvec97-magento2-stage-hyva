<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model;

class CategoryTemplateManagement implements \Ecwhim\SeoTemplates\Api\CategoryTemplateManagementInterface
{
    /**
     * @var CategoryTemplateApplier
     */
    private $templateApplier;

    /**
     * CategoryTemplateManagement constructor.
     *
     * @param CategoryTemplateApplier $templateApplier
     */
    public function __construct(\Ecwhim\SeoTemplates\Model\CategoryTemplateApplier $templateApplier)
    {
        $this->templateApplier = $templateApplier;
    }

    /**
     * @param \Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface $template
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function apply(\Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface $template): bool
    {
        return $this->templateApplier->apply($template);
    }
}

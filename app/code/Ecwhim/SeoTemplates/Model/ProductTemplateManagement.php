<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model;

class ProductTemplateManagement implements \Ecwhim\SeoTemplates\Api\ProductTemplateManagementInterface
{
    /**
     * @var ProductTemplateApplier
     */
    private $templateApplier;

    /**
     * ProductTemplateManagement constructor.
     *
     * @param ProductTemplateApplier $templateApplier
     */
    public function __construct(\Ecwhim\SeoTemplates\Model\ProductTemplateApplier $templateApplier)
    {
        $this->templateApplier = $templateApplier;
    }

    /**
     * @param \Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterface $template
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function apply(\Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterface $template): bool
    {
        return $this->templateApplier->apply($template);
    }
}

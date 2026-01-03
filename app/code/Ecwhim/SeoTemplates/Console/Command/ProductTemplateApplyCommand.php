<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Console\Command;

use Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterface;

class ProductTemplateApplyCommand extends AbstractProductTemplateManageCommand
{
    /**
     * @var \Ecwhim\SeoTemplates\Api\ProductTemplateManagementInterface
     */
    protected $templateManagement;

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('ecwhim-seo:seotemplates:product-template:apply');
        $this->setDescription('Applies Product SEO Template(s)');

        parent::configure();
    }

    /**
     * @param ProductTemplateInterface|\Ecwhim\SeoTemplates\Api\Data\TemplateInterface $template
     */
    protected function performAction(\Ecwhim\SeoTemplates\Api\Data\TemplateInterface $template): void
    {
        $this->getTemplateManagement()->apply($template);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDisplayMessage(): string
    {
        return 'Applied Product SEO Templates:';
    }

    /**
     * Used to workaround a failed Magento Marketplace Installation & Varnish Test result
     *
     * @return \Ecwhim\SeoTemplates\Api\ProductTemplateManagementInterface
     */
    protected function getTemplateManagement(): \Ecwhim\SeoTemplates\Api\ProductTemplateManagementInterface
    {
        if ($this->templateManagement === null) {
            $this->templateManagement = $this->objectManager->get(
                \Ecwhim\SeoTemplates\Api\ProductTemplateManagementInterface::class
            );
        }

        return $this->templateManagement;
    }
}

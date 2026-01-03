<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Console\Command;

use Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface;

class CategoryTemplateApplyCommand extends AbstractCategoryTemplateManageCommand
{
    /**
     * @var \Ecwhim\SeoTemplates\Api\CategoryTemplateManagementInterface
     */
    protected $templateManagement;

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('ecwhim-seo:seotemplates:category-template:apply');
        $this->setDescription('Applies Category SEO Template(s)');

        parent::configure();
    }

    /**
     * @param CategoryTemplateInterface|\Ecwhim\SeoTemplates\Api\Data\TemplateInterface $template
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
        return 'Applied Category SEO Templates:';
    }

    /**
     * Used to workaround a failed Magento Marketplace Installation & Varnish Test result
     *
     * @return \Ecwhim\SeoTemplates\Api\CategoryTemplateManagementInterface
     */
    protected function getTemplateManagement(): \Ecwhim\SeoTemplates\Api\CategoryTemplateManagementInterface
    {
        if ($this->templateManagement === null) {
            $this->templateManagement = $this->objectManager->get(
                \Ecwhim\SeoTemplates\Api\CategoryTemplateManagementInterface::class
            );
        }

        return $this->templateManagement;
    }
}

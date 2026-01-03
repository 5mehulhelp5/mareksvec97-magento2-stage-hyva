<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Console\Command;

use Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface;

class CategoryTemplateDeleteCommand extends AbstractCategoryTemplateManageCommand
{
    /**
     * @var bool
     */
    protected $needInteract = true;

    /**
     * @var \Ecwhim\SeoTemplates\Api\CategoryTemplateRepositoryInterface
     */
    protected $templateRepository;

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('ecwhim-seo:seotemplates:category-template:delete');
        $this->setDescription('Deletes Category SEO Template(s)');

        parent::configure();
    }

    /**
     * @param CategoryTemplateInterface|\Ecwhim\SeoTemplates\Api\Data\TemplateInterface $template
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    protected function performAction(\Ecwhim\SeoTemplates\Api\Data\TemplateInterface $template): void
    {
        $this->getTemplateRepository()->delete($template);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDisplayMessage(): string
    {
        return 'Deleted Category SEO Templates:';
    }

    /**
     * Used to workaround a failed Magento Marketplace Installation & Varnish Test result
     *
     * @return \Ecwhim\SeoTemplates\Api\CategoryTemplateRepositoryInterface
     */
    protected function getTemplateRepository(): \Ecwhim\SeoTemplates\Api\CategoryTemplateRepositoryInterface
    {
        if ($this->templateRepository === null) {
            $this->templateRepository = $this->objectManager->get(
                \Ecwhim\SeoTemplates\Api\CategoryTemplateRepositoryInterface::class
            );
        }

        return $this->templateRepository;
    }
}

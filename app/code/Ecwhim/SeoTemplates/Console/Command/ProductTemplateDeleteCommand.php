<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Console\Command;

use Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterface;

class ProductTemplateDeleteCommand extends AbstractProductTemplateManageCommand
{
    /**
     * @var bool
     */
    protected $needInteract = true;

    /**
     * @var \Ecwhim\SeoTemplates\Api\ProductTemplateRepositoryInterface
     */
    protected $templateRepository;

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('ecwhim-seo:seotemplates:product-template:delete');
        $this->setDescription('Deletes Product SEO Template(s)');

        parent::configure();
    }

    /**
     * @param ProductTemplateInterface|\Ecwhim\SeoTemplates\Api\Data\TemplateInterface $template
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
        return 'Deleted Product SEO Templates:';
    }

    /**
     * Used to workaround a failed Magento Marketplace Installation & Varnish Test result
     *
     * @return \Ecwhim\SeoTemplates\Api\ProductTemplateRepositoryInterface
     */
    protected function getTemplateRepository(): \Ecwhim\SeoTemplates\Api\ProductTemplateRepositoryInterface
    {
        if ($this->templateRepository === null) {
            $this->templateRepository = $this->objectManager->get(
                \Ecwhim\SeoTemplates\Api\ProductTemplateRepositoryInterface::class
            );
        }

        return $this->templateRepository;
    }
}

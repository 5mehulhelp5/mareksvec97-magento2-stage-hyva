<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Block\Adminhtml\Edit\CategoryTemplate;

use Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface;

class DeleteButton extends \Ecwhim\SeoTemplates\Block\Adminhtml\Edit\AbstractDeleteButton
{
    /**
     * @var \Ecwhim\SeoTemplates\Api\CategoryTemplateRepositoryInterface
     */
    protected $templateRepository;

    /**
     * DeleteButton constructor.
     *
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Ecwhim\SeoTemplates\Api\CategoryTemplateRepositoryInterface $templateRepository
     */
    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\App\RequestInterface $request,
        \Ecwhim\SeoTemplates\Api\CategoryTemplateRepositoryInterface $templateRepository
    ) {
        parent::__construct($urlBuilder, $request);

        $this->templateRepository = $templateRepository;
    }

    /**
     * @inheritDoc
     */
    protected function getTemplateId(): ?int
    {
        try {
            $templateId = $this->request->getParam(CategoryTemplateInterface::TEMPLATE_ID);

            if (empty($templateId)) {
                return null;
            }

            $template = $this->templateRepository->getById((int)$templateId);

            return $template->getTemplateId();
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    protected function getDeleteUrl(): string
    {
        return $this->urlBuilder->getUrl(
            '*/*/delete',
            [CategoryTemplateInterface::TEMPLATE_ID => $this->getTemplateId()]
        );
    }
}

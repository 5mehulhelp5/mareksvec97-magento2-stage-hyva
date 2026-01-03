<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Controller\Adminhtml\ProductTemplate;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterface;

class Apply extends \Ecwhim\SeoTemplates\Controller\Adminhtml\ProductTemplate implements HttpPostActionInterface
{
    /**
     * @var \Ecwhim\SeoTemplates\Api\ProductTemplateRepositoryInterface
     */
    protected $templateRepository;

    /**
     * @var \Ecwhim\SeoTemplates\Api\ProductTemplateManagementInterface
     */
    protected $templateManagement;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Apply constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Ecwhim\SeoTemplates\Api\ProductTemplateRepositoryInterface $templateRepository
     * @param \Ecwhim\SeoTemplates\Api\ProductTemplateManagementInterface $templateManagement
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Ecwhim\SeoTemplates\Api\ProductTemplateRepositoryInterface $templateRepository,
        \Ecwhim\SeoTemplates\Api\ProductTemplateManagementInterface $templateManagement,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);

        $this->templateRepository = $templateRepository;
        $this->templateManagement = $templateManagement;
        $this->logger             = $logger;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $templateId = (int)$this->getRequest()->getParam(ProductTemplateInterface::TEMPLATE_ID);

        if ($templateId) {
            try {
                $this->validateRequest();

                $template = $this->templateRepository->getById($templateId);

                $this->templateManagement->apply($template);

                $this->messageManager->addSuccessMessage(
                    __('The template "%1" has been applied.', $template->getName())
                );
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->logger->critical($e);
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while applying this template.')
                );
                $this->logger->critical($e);
            }
        } else {
            $this->messageManager->addErrorMessage(__('We can\'t find a template to apply.'));
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    protected function validateRequest(): bool
    {
        if (!$this->getRequest()->isPost()) {
            throw new \Magento\Framework\Exception\NotFoundException(__('Page Not Found.'));
        }

        return true;
    }
}

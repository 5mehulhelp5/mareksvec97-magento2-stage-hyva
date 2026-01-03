<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Controller\Adminhtml\CategoryTemplate;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface;

class Delete extends \Ecwhim\SeoTemplates\Controller\Adminhtml\CategoryTemplate implements HttpPostActionInterface
{
    /**
     * @var \Ecwhim\SeoTemplates\Api\CategoryTemplateRepositoryInterface
     */
    protected $templateRepository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Delete constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Ecwhim\SeoTemplates\Api\CategoryTemplateRepositoryInterface $templateRepository
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Ecwhim\SeoTemplates\Api\CategoryTemplateRepositoryInterface $templateRepository,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);

        $this->templateRepository = $templateRepository;
        $this->logger             = $logger;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $templateId     = (int)$this->getRequest()->getParam(CategoryTemplateInterface::TEMPLATE_ID);

        if ($templateId) {
            try {
                $this->validateRequest();
                $this->templateRepository->deleteById($templateId);
                $this->messageManager->addSuccessMessage(__('The template "%1" has been deleted.', $templateId));
            } catch (\Magento\Framework\Exception\NotFoundException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Magento\Framework\Exception\CouldNotDeleteException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->logger->critical($e);

                return $resultRedirect->setPath('*/*/edit', [CategoryTemplateInterface::TEMPLATE_ID => $templateId]);
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while deleting this template.')
                );
                $this->logger->critical($e);

                return $resultRedirect->setPath('*/*/edit', [CategoryTemplateInterface::TEMPLATE_ID => $templateId]);
            }
        } else {
            $this->messageManager->addErrorMessage(__('We can\'t find a template to delete.'));
        }

        return $resultRedirect->setPath('*/*/');
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

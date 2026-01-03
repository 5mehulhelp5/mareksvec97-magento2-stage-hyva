<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Controller\Adminhtml\ProductTemplate;

use Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;

class Edit extends \Ecwhim\SeoTemplates\Controller\Adminhtml\ProductTemplate implements HttpGetActionInterface
{
    const MENU_ID = 'Ecwhim_SeoTemplates::seo_templates_product_template';

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Ecwhim\SeoTemplates\Api\ProductTemplateRepositoryInterface
     */
    protected $templateRepository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Edit constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Ecwhim\SeoTemplates\Api\ProductTemplateRepositoryInterface $templateRepository
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Ecwhim\SeoTemplates\Api\ProductTemplateRepositoryInterface $templateRepository,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);

        $this->resultPageFactory  = $resultPageFactory;
        $this->templateRepository = $templateRepository;
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
                $template = $this->templateRepository->getById($templateId);

                $resultPage = $this->resultPageFactory->create();
                $resultPage->setActiveMenu(static::MENU_ID);
                $resultPage->getConfig()->getTitle()->prepend(__('Product SEO Templates'));
                $resultPage->getConfig()->getTitle()->prepend(__('Edit Product Template "%1"', $template->getName()));

                return $resultPage;
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while editing this template.'));
                $this->logger->critical($e);
            }
        } else {
            $this->messageManager->addErrorMessage(__('We can\'t find a template to edit.'));
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}

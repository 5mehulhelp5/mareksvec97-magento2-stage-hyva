<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Controller\Adminhtml\ProductTemplate;

class MassApply extends MassAction implements \Magento\Framework\App\Action\HttpPostActionInterface
{
    /**
     * @var \Ecwhim\SeoTemplates\Api\ProductTemplateManagementInterface
     */
    protected $templateManagement;

    /**
     * MassApply constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Ecwhim\SeoTemplates\Model\ResourceModel\ProductTemplate\CollectionFactory $collectionFactory
     * @param \Ecwhim\SeoTemplates\Api\ProductTemplateManagementInterface $templateManagement
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Psr\Log\LoggerInterface $logger,
        \Ecwhim\SeoTemplates\Model\ResourceModel\ProductTemplate\CollectionFactory $collectionFactory,
        \Ecwhim\SeoTemplates\Api\ProductTemplateManagementInterface $templateManagement
    ) {
        parent::__construct($context, $filter, $logger, $collectionFactory);

        $this->templateManagement = $templateManagement;
    }

    /**
     * @inheritDoc
     */
    protected function massAction(\Ecwhim\SeoTemplates\Model\ResourceModel\AbstractTemplateCollection $collection): void
    {
        $templateApplied = 0;

        /** @var \Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterface $template */
        foreach ($collection->getItems() as $template) {
            $this->templateManagement->apply($template);
            $templateApplied++;
        }

        if ($templateApplied) {
            $this->messageManager->addSuccessMessage(
                __('A total of %1 template(s) have been applied.', $templateApplied)
            );
        }
    }

    /**
     * @inheritDoc
     */
    protected function getExceptionMessage(): string
    {
        return __('Something went wrong while applying these templates.')->__toString();
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

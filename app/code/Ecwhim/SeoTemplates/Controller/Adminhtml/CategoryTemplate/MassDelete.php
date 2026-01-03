<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Controller\Adminhtml\CategoryTemplate;

class MassDelete extends MassAction implements \Magento\Framework\App\Action\HttpPostActionInterface
{
    /**
     * @var \Ecwhim\SeoTemplates\Api\CategoryTemplateRepositoryInterface
     */
    protected $templateRepository;

    /**
     * MassDelete constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Ecwhim\SeoTemplates\Model\ResourceModel\CategoryTemplate\CollectionFactory $collectionFactory
     * @param \Ecwhim\SeoTemplates\Api\CategoryTemplateRepositoryInterface $templateRepository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Psr\Log\LoggerInterface $logger,
        \Ecwhim\SeoTemplates\Model\ResourceModel\CategoryTemplate\CollectionFactory $collectionFactory,
        \Ecwhim\SeoTemplates\Api\CategoryTemplateRepositoryInterface $templateRepository
    ) {
        parent::__construct($context, $filter, $logger, $collectionFactory);

        $this->templateRepository = $templateRepository;
    }

    /**
     * @param \Ecwhim\SeoTemplates\Model\ResourceModel\AbstractTemplateCollection $collection
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    protected function massAction(\Ecwhim\SeoTemplates\Model\ResourceModel\AbstractTemplateCollection $collection): void
    {
        $templateDeleted = 0;

        /** @var \Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface $template */
        foreach ($collection->getItems() as $template) {
            $this->templateRepository->delete($template);
            $templateDeleted++;
        }

        if ($templateDeleted) {
            $this->messageManager->addSuccessMessage(
                __('A total of %1 template(s) have been deleted.', $templateDeleted)
            );
        }
    }

    /**
     * @inheritDoc
     */
    protected function getExceptionMessage(): string
    {
        return __('Something went wrong while deleting these templates.')->__toString();
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

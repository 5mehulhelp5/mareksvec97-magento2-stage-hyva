<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Controller\Adminhtml;

abstract class MassAction extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * MassAction constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);

        $this->filter = $filter;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            $this->validateRequest();

            /** @var \Ecwhim\SeoTemplates\Model\ResourceModel\AbstractTemplateCollection $collection */
            $collection = $this->filter->getCollection($this->getCollection());

            $this->massAction($collection);
        } catch (\Magento\Framework\Exception\NotFoundException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->logger->critical($e);
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, $this->getExceptionMessage());
            $this->logger->critical($e);
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }

    /**
     * @return \Ecwhim\SeoTemplates\Model\ResourceModel\AbstractTemplateCollection
     */
    abstract protected function getCollection(): \Ecwhim\SeoTemplates\Model\ResourceModel\AbstractTemplateCollection;

    /**
     * @param \Ecwhim\SeoTemplates\Model\ResourceModel\AbstractTemplateCollection $collection
     */
    abstract protected function massAction(
        \Ecwhim\SeoTemplates\Model\ResourceModel\AbstractTemplateCollection $collection
    ): void;

    /**
     * @return string
     */
    abstract protected function getExceptionMessage(): string;

    /**
     * @return bool
     */
    protected function validateRequest(): bool
    {
        return true;
    }
}

<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Cron;

class ApplyProductTemplate
{
    /**
     * @var \Ecwhim\SeoTemplates\Model\ResourceModel\ProductTemplate\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Ecwhim\SeoTemplates\Api\ProductTemplateManagementInterface
     */
    private $templateManagement;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * ApplyProductTemplate constructor.
     *
     * @param \Ecwhim\SeoTemplates\Model\ResourceModel\ProductTemplate\CollectionFactory $collectionFactory
     * @param \Ecwhim\SeoTemplates\Api\ProductTemplateManagementInterface $templateManagement
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Ecwhim\SeoTemplates\Model\ResourceModel\ProductTemplate\CollectionFactory $collectionFactory,
        \Ecwhim\SeoTemplates\Api\ProductTemplateManagementInterface $templateManagement,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->collectionFactory  = $collectionFactory;
        $this->templateManagement = $templateManagement;
        $this->logger             = $logger;
    }

    /**
     * @return void
     */
    public function execute(): void
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(\Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterface::APPLY_BY_CRON, 1);

        /** @var \Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterface $template */
        foreach ($collection->getItems() as $template) {
            try {
                $this->templateManagement->apply($template);
            } catch (\Exception $e) {
                $this->logger->critical($e);
            }
        }
    }
}

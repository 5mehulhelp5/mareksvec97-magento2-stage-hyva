<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Cron;

class ApplyCategoryTemplate
{
    /**
     * @var \Ecwhim\SeoTemplates\Model\ResourceModel\CategoryTemplate\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Ecwhim\SeoTemplates\Api\CategoryTemplateManagementInterface
     */
    private $templateManagement;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * ApplyCategoryTemplate constructor.
     *
     * @param \Ecwhim\SeoTemplates\Model\ResourceModel\CategoryTemplate\CollectionFactory $collectionFactory
     * @param \Ecwhim\SeoTemplates\Api\CategoryTemplateManagementInterface $templateManagement
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Ecwhim\SeoTemplates\Model\ResourceModel\CategoryTemplate\CollectionFactory $collectionFactory,
        \Ecwhim\SeoTemplates\Api\CategoryTemplateManagementInterface $templateManagement,
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
        $collection->addFieldToFilter(\Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface::APPLY_BY_CRON, 1);

        /** @var \Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface $template */
        foreach ($collection->getItems() as $template) {
            try {
                $this->templateManagement->apply($template);
            } catch (\Exception $e) {
                $this->logger->critical($e);
            }
        }
    }
}

<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Console\Command;

abstract class AbstractCategoryTemplateManageCommand extends AbstractTemplateManageCommand
{
    /**
     * @var \Ecwhim\SeoTemplates\Model\ResourceModel\CategoryTemplate\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * AbstractCategoryTemplateManageCommand constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\App\State $appState
     * @param \Ecwhim\SeoTemplates\Model\ResourceModel\CategoryTemplate\CollectionFactory $collectionFactory
     * @param string|null $name
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\State $appState,
        \Ecwhim\SeoTemplates\Model\ResourceModel\CategoryTemplate\CollectionFactory $collectionFactory,
        string $name = null
    ) {
        parent::__construct($objectManager, $appState, $name);

        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @inheritDoc
     */
    protected function getCollection(): \Ecwhim\SeoTemplates\Model\ResourceModel\AbstractTemplateCollection
    {
        return $this->collectionFactory->create();
    }
}

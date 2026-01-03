<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Controller\Adminhtml\CategoryTemplate;

abstract class MassAction extends \Ecwhim\SeoTemplates\Controller\Adminhtml\MassAction
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Ecwhim_SeoTemplates::seo_templates_category_template';

    /**
     * @var \Ecwhim\SeoTemplates\Model\ResourceModel\CategoryTemplate\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * MassAction constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Ecwhim\SeoTemplates\Model\ResourceModel\CategoryTemplate\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Psr\Log\LoggerInterface $logger,
        \Ecwhim\SeoTemplates\Model\ResourceModel\CategoryTemplate\CollectionFactory $collectionFactory
    ) {
        parent::__construct($context, $filter, $logger);

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

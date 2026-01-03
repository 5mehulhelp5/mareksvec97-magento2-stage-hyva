<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model\ResourceModel\ProductTemplate\Relation\Store;

class ReadHandler implements \Magento\Framework\EntityManager\Operation\ExtensionInterface
{
    /**
     * @var \Ecwhim\SeoTemplates\Model\ResourceModel\ProductTemplate
     */
    protected $templateResource;

    /**
     * ReadHandler constructor.
     *
     * @param \Ecwhim\SeoTemplates\Model\ResourceModel\ProductTemplate $templateResource
     */
    public function __construct(\Ecwhim\SeoTemplates\Model\ResourceModel\ProductTemplate $templateResource)
    {
        $this->templateResource = $templateResource;
    }

    /**
     * @param \Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterface|object $entity
     * @param array $arguments
     * @return object
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute($entity, $arguments = [])
    {
        $storeIds = $this->templateResource->lookupStoreIds($entity->getTemplateId());

        $entity->setStoreIds($storeIds);

        return $entity;
    }
}

<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model\ResourceModel\CategoryTemplate\Relation\Store;

use Ecwhim\SeoTemplates\Setup\Patch\Schema\AddCategoryTemplateStoreTable;
use Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface;

class SaveHandler implements \Magento\Framework\EntityManager\Operation\ExtensionInterface
{
    /**
     * @var \Ecwhim\SeoTemplates\Model\ResourceModel\CategoryTemplate
     */
    protected $templateResource;

    /**
     * SaveHandler constructor.
     *
     * @param \Ecwhim\SeoTemplates\Model\ResourceModel\CategoryTemplate $templateResource
     */
    public function __construct(\Ecwhim\SeoTemplates\Model\ResourceModel\CategoryTemplate $templateResource)
    {
        $this->templateResource = $templateResource;
    }

    /**
     * @param \Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface|object $entity
     * @param array $arguments
     * @return object
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute($entity, $arguments = [])
    {
        $templateId  = $entity->getTemplateId();
        $oldStoreIds = $this->templateResource->lookupStoreIds($templateId);
        $newStoreIds = $entity->getStoreIds();

        $this->processDelete(array_diff($oldStoreIds, $newStoreIds), $templateId);
        $this->processInsert(array_diff($newStoreIds, $oldStoreIds), $templateId);

        return $entity;
    }

    /**
     * @param array $storeIds
     * @param int $templateId
     */
    protected function processDelete(array $storeIds, int $templateId): void
    {
        if ($storeIds) {
            $this->templateResource->getConnection()->delete(
                $this->templateResource->getTable(AddCategoryTemplateStoreTable::TABLE_CATEGORY_TEMPLATE_STORE),
                [
                    CategoryTemplateInterface::TEMPLATE_ID . ' = ?'           => $templateId,
                    AddCategoryTemplateStoreTable::COLUMN_STORE_ID . ' IN(?)' => $storeIds
                ]
            );
        }
    }

    /**
     * @param array $storeIds
     * @param int $templateId
     */
    protected function processInsert(array $storeIds, int $templateId): void
    {
        if ($storeIds) {
            $data = [];

            foreach ($storeIds as $storeId) {
                $data[] = [
                    CategoryTemplateInterface::TEMPLATE_ID         => $templateId,
                    AddCategoryTemplateStoreTable::COLUMN_STORE_ID => $storeId
                ];
            }

            $this->templateResource->getConnection()->insertMultiple(
                $this->templateResource->getTable(AddCategoryTemplateStoreTable::TABLE_CATEGORY_TEMPLATE_STORE),
                $data
            );
        }
    }
}

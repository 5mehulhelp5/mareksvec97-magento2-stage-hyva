<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model\ResourceModel\CategoryTemplate\Relation\Category;

use Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface;
use Ecwhim\SeoTemplates\Setup\Patch\Schema\AddCategoryTemplateCategoryTable;

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
     * @param CategoryTemplateInterface|object $entity
     * @param array $arguments
     * @return object
     */
    public function execute($entity, $arguments = [])
    {
        $templateId = $entity->getTemplateId();

        if ($entity->getApplyToAllCategories()) {
            $this->processDelete($templateId);

            return $entity;
        }

        $newCategoryIds = $entity->getCategoryIds();

        if (empty($newCategoryIds)) {
            return $entity;
        }

        $oldCategoryIds = $this->templateResource->getAssignedCategoryIds([$templateId]);

        $this->processDelete($templateId, true, array_diff($oldCategoryIds, $newCategoryIds));
        $this->processInsert($templateId, array_diff($newCategoryIds, $oldCategoryIds));

        return $entity;
    }

    /**
     * @param int $templateId
     * @param bool $isLimited
     * @param array $categoryIds
     */
    private function processDelete(int $templateId, bool $isLimited = false, array $categoryIds = []): void
    {
        if ($isLimited && empty($categoryIds)) {
            return;
        }

        $where = [CategoryTemplateInterface::TEMPLATE_ID . ' = ?' => $templateId];

        if ($isLimited) {
            $where[AddCategoryTemplateCategoryTable::COLUMN_CATEGORY_ID . ' IN(?)'] = $categoryIds;
        }

        $this->templateResource->getConnection()->delete(
            $this->templateResource->getTable(AddCategoryTemplateCategoryTable::TABLE_CATEGORY_TEMPLATE_CATEGORY),
            $where
        );
    }

    /**
     * @param int $templateId
     * @param array $categoryIds
     */
    protected function processInsert(int $templateId, array $categoryIds): void
    {
        if ($categoryIds) {
            $data = [];

            foreach ($categoryIds as $categoryId) {
                $data[] = [
                    CategoryTemplateInterface::TEMPLATE_ID               => $templateId,
                    AddCategoryTemplateCategoryTable::COLUMN_CATEGORY_ID => $categoryId
                ];
            }

            $this->templateResource->getConnection()->insertMultiple(
                $this->templateResource->getTable(AddCategoryTemplateCategoryTable::TABLE_CATEGORY_TEMPLATE_CATEGORY),
                $data
            );
        }
    }
}

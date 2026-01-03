<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model;

use Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface;
use Ecwhim\SeoTemplates\Model\Source\TemplateStatus;
use Magento\Catalog\Api\Data\CategoryInterface;

class CategoryTemplateApplier
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category
     */
    private $categoryResource;

    /**
     * @var \Ecwhim\SeoTemplates\Model\TemplateStoreIdsResolver
     */
    private $templateStoreIdsResolver;

    /**
     * @var \Ecwhim\SeoTemplates\Model\TemplateFilterInterface
     */
    private $templateFilter;

    /**
     * @var \Ecwhim\SeoTemplates\Model\ResourceModel\CategoryTemplate
     */
    private $templateResource;

    /**
     * @var \Ecwhim\SeoTemplates\Model\ResourceModel\CategoryTemplate\CollectionFactory
     */
    private $templateCollectionFactory;

    /**
     * @var \Ecwhim\SeoTemplates\Model\RootCategoryIdResolver
     */
    private $rootCategoryIdResolver;

    /**
     * @var int
     */
    private $batchSize;

    /**
     * CategoryTemplateApplier constructor.
     *
     * @param \Magento\Catalog\Model\ResourceModel\Category $categoryResource
     * @param \Ecwhim\SeoTemplates\Model\TemplateStoreIdsResolver $templateStoreIdsResolver
     * @param \Ecwhim\SeoTemplates\Model\TemplateFilterInterface $templateFilter
     * @param \Ecwhim\SeoTemplates\Model\ResourceModel\CategoryTemplate $templateResource
     * @param \Ecwhim\SeoTemplates\Model\ResourceModel\CategoryTemplate\CollectionFactory $templateCollectionFactory
     * @param \Ecwhim\SeoTemplates\Model\RootCategoryIdResolver $rootCategoryIdResolver
     * @param int $batchSize
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Category $categoryResource,
        \Ecwhim\SeoTemplates\Model\TemplateStoreIdsResolver $templateStoreIdsResolver,
        \Ecwhim\SeoTemplates\Model\TemplateFilterInterface $templateFilter,
        \Ecwhim\SeoTemplates\Model\ResourceModel\CategoryTemplate $templateResource,
        \Ecwhim\SeoTemplates\Model\ResourceModel\CategoryTemplate\CollectionFactory $templateCollectionFactory,
        \Ecwhim\SeoTemplates\Model\RootCategoryIdResolver $rootCategoryIdResolver,
        int $batchSize = 1000
    ) {
        $this->categoryResource          = $categoryResource;
        $this->templateStoreIdsResolver  = $templateStoreIdsResolver;
        $this->templateFilter            = $templateFilter;
        $this->templateResource          = $templateResource;
        $this->templateCollectionFactory = $templateCollectionFactory;
        $this->rootCategoryIdResolver    = $rootCategoryIdResolver;
        $this->batchSize                 = $batchSize;
    }

    /**
     * @param CategoryTemplateInterface $template
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function apply(CategoryTemplateInterface $template): bool
    {
        $this->validateTemplate($template);

        $type      = $template->getType();
        $attribute = $this->categoryResource->getAttribute($type);

        if (!$attribute) {
            return false;
        }

        $attrTableName = $attribute->getBackend()->getTable();
        $updateFields  = $attribute->isStatic() ? [$attribute->getAttributeCode()] : ['value'];
        $storeIds      = $this->templateStoreIdsResolver->getStoreIds($template);
        $content       = $template->getContent();
        $connection    = $this->categoryResource->getConnection();

        foreach ($storeIds as $storeId) {
            $storeId                   = (int)$storeId;
            $higherPriorityTemplateIds = [];

            foreach ($this->getDataForHigherPriorityTemplates($template, $storeId) as $templateData) {
                if ($templateData[CategoryTemplateInterface::APPLY_TO_ALL_CATEGORIES]) {
                    continue 2;
                }

                $higherPriorityTemplateIds[] = $templateData[CategoryTemplateInterface::TEMPLATE_ID];
            }

            $select        = $this->prepareSelect($template, $storeId, $higherPriorityTemplateIds);
            $categoryCount = ($select === null) ? 0 : $this->getCategoryCount($select);
            $offset        = null;

            while ($categoryCount > 0) {
                $select->limit($this->batchSize, $offset);

                $categoryIds = $connection->fetchCol($select);

                if (empty($categoryIds)) {
                    $offset        += $this->batchSize;
                    $categoryCount -= $this->batchSize;

                    continue;
                }

                $values = $this->templateFilter->massFilter($categoryIds, $content, $storeId, $type);

                if ($attribute->isStatic()) {
                    $data = $this->prepareDataToSaveForStaticAttr(
                        $categoryIds,
                        $values,
                        $attribute->getAttributeCode()
                    );
                } else {
                    $data = $this->prepareDataToSave(
                        $categoryIds,
                        $values,
                        (int)$attribute->getAttributeId(),
                        $storeId
                    );
                }

                if ($data) {
                    $connection->insertOnDuplicate($attrTableName, $data, $updateFields);
                }

                $offset        += $this->batchSize;
                $categoryCount -= $this->batchSize;
            }
        }

        $this->templateResource->afterApplicationProcess($template);

        return true;
    }

    /**
     * @param CategoryTemplateInterface $template
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function validateTemplate(CategoryTemplateInterface $template): bool
    {
        if (!$template->getIsActive()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Inactive template "%1" cannot be applied.', $template->getName())
            );
        }

        return true;
    }

    /**
     * @param CategoryTemplateInterface $template
     * @param int $storeId
     * @return array
     */
    private function getDataForHigherPriorityTemplates(CategoryTemplateInterface $template, int $storeId): array
    {
        $collection = $this->templateCollectionFactory->create();
        $connection = $collection->getConnection();

        $collection
            ->addFieldToSelect(CategoryTemplateInterface::TEMPLATE_ID)
            ->addFieldToSelect(CategoryTemplateInterface::APPLY_TO_ALL_CATEGORIES)
            ->addFieldToFilter(CategoryTemplateInterface::IS_ACTIVE, TemplateStatus::ACTIVE)
            ->addFieldToFilter(CategoryTemplateInterface::TYPE, $template->getType())
            ->addFieldToFilter(CategoryTemplateInterface::SCOPE, $template->getScope());

        if ($template->getScope() !== \Ecwhim\SeoTemplates\Model\Source\Scope::SCOPE_GLOBAL) {
            $collection->addStoreFilter($storeId);
        }

        $collection->getSelect()->where($this->getPriorityCond($connection, $template));

        return $collection->getData();
    }

    /**
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     * @param CategoryTemplateInterface $template
     * @param string $tableAlias
     * @return string
     */
    private function getPriorityCond(
        \Magento\Framework\DB\Adapter\AdapterInterface $connection,
        CategoryTemplateInterface $template,
        string $tableAlias = 'main_table'
    ): string {
        $priority   = $template->getPriority();
        $templateId = $template->getTemplateId();
        $andCond    = [
            $connection->quoteInto($tableAlias . '.' . CategoryTemplateInterface::PRIORITY . ' = ?', $priority),
            $connection->quoteInto($tableAlias . '.' . CategoryTemplateInterface::TEMPLATE_ID . ' < ?', $templateId)
        ];
        $orCond     = [
            $connection->quoteInto($tableAlias . '.' . CategoryTemplateInterface::PRIORITY . ' < ?', $priority),
            '( ' . implode(' AND ', $andCond) . ' )'
        ];

        return implode(' OR ', $orCond);
    }

    /**
     * @param CategoryTemplateInterface $template
     * @param int $storeId
     * @param array $higherPriorityTemplateIds
     * @return \Magento\Framework\DB\Select|null
     */
    private function prepareSelect(
        CategoryTemplateInterface $template,
        int $storeId,
        array $higherPriorityTemplateIds = []
    ): ?\Magento\Framework\DB\Select {
        $excludeIds  = [];
        $idFieldName = $this->categoryResource->getIdFieldName();
        $select      = $this->categoryResource->getConnection()->select();
        $select
            ->from(['e' => $this->categoryResource->getEntityTable()], $idFieldName)
            ->where('e.' . CategoryInterface::KEY_PARENT_ID . ' > ?', \Magento\Catalog\Model\Category::TREE_ROOT_ID);

        if ($higherPriorityTemplateIds) {
            $excludeIds = $this->templateResource->getAssignedCategoryIds($higherPriorityTemplateIds);
        }

        if ($template->getApplyToAllCategories()) {
            if ($excludeIds) {
                $select->where('e.' . $idFieldName . ' NOT IN(?)', $excludeIds);
            }
        } else {
            $categoryIds = $this->templateResource->getAssignedCategoryIds([$template->getTemplateId()], $excludeIds);

            if (empty($categoryIds)) {
                return null;
            }

            $select->where('e.' . $idFieldName . ' IN(?)', $categoryIds);
        }

        if ($storeId !== \Magento\Store\Model\Store::DEFAULT_STORE_ID) {
            $rootCategoryId      = \Magento\Catalog\Model\Category::TREE_ROOT_ID;
            $storeRootCategoryId = $this->rootCategoryIdResolver->getRootCategoryId($storeId);
            $path                = "{$rootCategoryId}/{$storeRootCategoryId}/%";

            $select->where('e.' . CategoryInterface::KEY_PATH . ' LIKE ?', $path);
        }

        return $select;
    }

    /**
     * @param \Magento\Framework\DB\Select $select
     * @return int
     */
    private function getCategoryCount(\Magento\Framework\DB\Select $select): int
    {
        $countSelect = clone $select;
        $countSelect->reset(\Magento\Framework\DB\Select::COLUMNS);
        $countSelect->columns('COUNT(*)');

        return (int)$this->categoryResource->getConnection()->fetchOne($countSelect);
    }

    /**
     * @param array $categoryIds
     * @param array $values
     * @param string $attrCode
     * @return array
     */
    private function prepareDataToSaveForStaticAttr(array $categoryIds, array $values, string $attrCode): array
    {
        $data      = [];
        $linkField = $this->categoryResource->getLinkField();

        if ($this->categoryResource->getIdFieldName() == $linkField) {
            foreach ($values as $categoryId => $value) {
                $data[] = [$linkField => (int)$categoryId, $attrCode => $value];
            }
        } else {
            $linkFieldValues = $this->getLinkFieldValues($categoryIds);

            foreach ($values as $categoryId => $value) {
                if (!isset($linkFieldValues[$categoryId])) {
                    continue;
                }

                $data[] = [$linkField => (int)$linkFieldValues[$categoryId], $attrCode => $value];
            }
        }

        return $data;
    }

    /**
     * @param array $categoryIds
     * @param array $values
     * @param int $attributeId
     * @param int $storeId
     * @return array
     */
    private function prepareDataToSave(array $categoryIds, array $values, int $attributeId, int $storeId): array
    {
        $data      = [];
        $linkField = $this->categoryResource->getLinkField();

        if ($this->categoryResource->getIdFieldName() == $linkField) {
            foreach ($values as $categoryId => $value) {
                $data[] = [
                    'attribute_id' => $attributeId,
                    'store_id'     => $storeId,
                    $linkField     => (int)$categoryId,
                    'value'        => $value,
                ];
            }
        } else {
            $linkFieldValues = $this->getLinkFieldValues($categoryIds);

            foreach ($values as $categoryId => $value) {
                if (!isset($linkFieldValues[$categoryId])) {
                    continue;
                }

                $data[] = [
                    'attribute_id' => $attributeId,
                    'store_id'     => $storeId,
                    $linkField     => (int)$linkFieldValues[$categoryId],
                    'value'        => $value,
                ];
            }
        }

        return $data;
    }

    /**
     * @param array $entityIds
     * @return array
     */
    private function getLinkFieldValues(array $entityIds): array
    {
        $entityIdField = $this->categoryResource->getIdFieldName();
        $linkField     = $this->categoryResource->getLinkField();
        $tableName     = $this->categoryResource->getTable('catalog_category_entity');
        $select        = $this->categoryResource->getConnection()->select();
        $select
            ->from($tableName, [$entityIdField, $linkField])
            ->where($entityIdField . ' IN(?)', $entityIds);

        return $this->categoryResource->getConnection()->fetchPairs($select);
    }
}

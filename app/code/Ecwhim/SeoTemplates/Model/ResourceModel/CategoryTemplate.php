<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model\ResourceModel;

use Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface;
use Ecwhim\SeoTemplates\Setup\Patch\Schema\AddCategoryTemplateStoreTable;
use Ecwhim\SeoTemplates\Setup\Patch\Schema\AddCategoryTemplateCategoryTable;

class CategoryTemplate extends AbstractTemplate
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Ecwhim\SeoTemplates\Setup\Patch\Schema\AddCategoryTemplateTable::TABLE_CATEGORY_TEMPLATE,
            CategoryTemplateInterface::TEMPLATE_ID
        );
    }

    /**
     * @param int $templateId
     * @return array
     */
    public function lookupStoreIds(int $templateId): array
    {
        $select = $this->getConnection()->select();
        $select
            ->from(
                $this->getTable(AddCategoryTemplateStoreTable::TABLE_CATEGORY_TEMPLATE_STORE),
                AddCategoryTemplateStoreTable::COLUMN_STORE_ID
            )
            ->where(CategoryTemplateInterface::TEMPLATE_ID . ' = ?', $templateId);

        return $this->getConnection()->fetchCol($select);
    }

    /**
     * @param array $templateIds
     * @param array $categoryIds
     * @param bool $exclude
     * @return array
     */
    public function getAssignedCategoryIds(array $templateIds, array $categoryIds = [], bool $exclude = true): array
    {
        $select = $this->getConnection()->select();
        $select
            ->from(
                $this->getTable(AddCategoryTemplateCategoryTable::TABLE_CATEGORY_TEMPLATE_CATEGORY),
                AddCategoryTemplateCategoryTable::COLUMN_CATEGORY_ID
            )
            ->where(CategoryTemplateInterface::TEMPLATE_ID . ' IN(?)', $templateIds);

        if (count($templateIds) > 1) {
            $select->distinct(true);
        }

        if (!empty($categoryIds)) {
            $conditionRule = $exclude ? ' NOT IN(?)' : ' IN(?)';

            $select->where(AddCategoryTemplateCategoryTable::COLUMN_CATEGORY_ID . $conditionRule, $categoryIds);
        }

        return $this->getConnection()->fetchCol($select);
    }
}

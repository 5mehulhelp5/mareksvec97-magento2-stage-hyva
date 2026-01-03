<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model\ResourceModel\CategoryTemplate;

use Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface;
use Ecwhim\SeoTemplates\Api\CategoryTemplateManagementInterface;
use Ecwhim\SeoTemplates\Setup\Patch\Schema\AddCategoryTemplateStoreTable;

class Collection extends \Ecwhim\SeoTemplates\Model\ResourceModel\AbstractTemplateCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = CategoryTemplateInterface::TEMPLATE_ID;

    /**
     * @var string
     */
    protected $_eventObject = 'template_collection';

    /**
     * @var string
     */
    protected $_eventPrefix = CategoryTemplateManagementInterface::ENTITY_TYPE_CATEGORY_TEMPLATE . '_collection';

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(
            \Ecwhim\SeoTemplates\Model\CategoryTemplate::class,
            \Ecwhim\SeoTemplates\Model\ResourceModel\CategoryTemplate::class
        );
        $this->_map['fields'][self::STORE_FIELD] = self::STORE_TABLE_ALIAS .
            '.' . AddCategoryTemplateStoreTable::COLUMN_STORE_ID;
    }

    /**
     * @inheritDoc
     */
    protected function _afterLoad()
    {
        $this->addRelatedStoreIds(
            AddCategoryTemplateStoreTable::TABLE_CATEGORY_TEMPLATE_STORE,
            CategoryTemplateInterface::TEMPLATE_ID,
            AddCategoryTemplateStoreTable::COLUMN_STORE_ID
        );

        return parent::_afterLoad();
    }

    /**
     * @inheritDoc
     */
    protected function _renderFiltersBefore()
    {
        $this->joinStoreRelationTable(
            AddCategoryTemplateStoreTable::TABLE_CATEGORY_TEMPLATE_STORE,
            CategoryTemplateInterface::TEMPLATE_ID
        );

        parent::_renderFiltersBefore();
    }
}

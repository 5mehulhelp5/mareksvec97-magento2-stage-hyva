<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model\ResourceModel;

use Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterface;
use Ecwhim\SeoTemplates\Setup\Patch\Schema\AddProductTemplateStoreTable;

class ProductTemplate extends AbstractTemplate
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Ecwhim\SeoTemplates\Setup\Patch\Schema\AddProductTemplateTable::TABLE_PRODUCT_TEMPLATE,
            ProductTemplateInterface::TEMPLATE_ID
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
                $this->getTable(AddProductTemplateStoreTable::TABLE_PRODUCT_TEMPLATE_STORE),
                AddProductTemplateStoreTable::COLUMN_STORE_ID
            )
            ->where(ProductTemplateInterface::TEMPLATE_ID . ' = ?', $templateId);

        return $this->getConnection()->fetchCol($select);
    }
}

<?php

namespace Zymion\SeoFriendlyImages\Model\ResourceModel\Product;

use Magento\Store\Model\Store;

/**
 * Catalog product media gallery resource model.
 *
 * @api
 * @since 101.0.0
 */
class Gallery extends \Magento\Catalog\Model\ResourceModel\Product\Gallery
{

    /**
     * Create base load select
     *
     * @param int $storeId
     * @param int $attributeId
     * @return \Magento\Framework\DB\Select
     * @throws \Magento\Framework\Exception\LocalizedException
     * @since 101.0.1
     */
    public function createBatchBaseSelect($storeId, $attributeId)
    {
        $linkField = $this->metadata->getLinkField();

        $positionCheckSql = $this->getConnection()->getCheckSql(
            'value.position IS NULL',
            'default_value.position',
            'value.position'
        );

        $mainTableAlias = $this->getMainTableAlias();

        $select = $this->getConnection()->select()->from(
            [$mainTableAlias => $this->getMainTable()],
            [
                'value_id',
                'file' => 'value',
                'media_type'
            ]
        )->joinInner(
            ['entity' => $this->getTable(self::GALLERY_VALUE_TO_ENTITY_TABLE)],
            $mainTableAlias . '.value_id = entity.value_id',
            [$linkField]
        )->joinLeft(
            ['value' => $this->getTable(self::GALLERY_VALUE_TABLE)],
            implode(
                ' AND ',
                [
                    $mainTableAlias . '.value_id = value.value_id',
                    $this->getConnection()->quoteInto('value.store_id = ?', (int)$storeId),
                    'value.' . $linkField . ' = entity.' . $linkField,
                ]
            ),
            ['label', 'position', 'disabled', 'filename_overwrite']
        )->joinLeft(
            ['default_value' => $this->getTable(self::GALLERY_VALUE_TABLE)],
            implode(
                ' AND ',
                [
                    $mainTableAlias . '.value_id = default_value.value_id',
                    $this->getConnection()->quoteInto('default_value.store_id = ?', Store::DEFAULT_STORE_ID),
                    'default_value.' . $linkField . ' = entity.' . $linkField,
                ]
            ),
            [
                'label_default' => 'label',
                'position_default' => 'position',
                'disabled_default' => 'disabled',
                'filename_overwrite_default' => 'filename_overwrite'
            ]
        )->where(
            $mainTableAlias . '.attribute_id = ?',
            $attributeId
        )->where(
            $mainTableAlias . '.disabled = 0'
        )->order(
            $positionCheckSql . ' ' . \Magento\Framework\DB\Select::SQL_ASC
        );

        return $select;
    }
}

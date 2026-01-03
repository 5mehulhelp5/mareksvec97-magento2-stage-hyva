<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

abstract class AbstractTemplateCollection extends AbstractCollection
{
    const STORE_TABLE_ALIAS = 'store_table';
    const STORE_FIELD       = 'store';

    /**
     * @param int|array|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, bool $withAdmin = true)
    {
        if (!$this->getFilter(self::STORE_FIELD)) {
            $this->performAddStoreFilter($store, $withAdmin);
        }

        return $this;
    }

    /**
     * @param array|string $field
     * @param string|int|array|null $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'store_id') {
            return $this->addStoreFilter($condition, false);
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * @param int|array|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @return void
     */
    protected function performAddStoreFilter($store, bool $withAdmin = true): void
    {
        if ($store instanceof \Magento\Store\Model\Store) {
            $store = [$store->getId()];
        }

        if (!is_array($store)) {
            $store = [$store];
        }

        if ($withAdmin) {
            $store[] = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
        }

        $this->addFilter(self::STORE_FIELD, ['in' => $store], 'public');
    }

    /**
     * Join store relation table if there is store filter
     *
     * @param string $tableName
     * @param string $linkField
     * @return void
     */
    protected function joinStoreRelationTable(string $tableName, string $linkField): void
    {
        if ($this->getFilter(self::STORE_FIELD)) {
            $this->getSelect()
                 ->join(
                     [self::STORE_TABLE_ALIAS => $this->getTable($tableName)],
                     'main_table.' . $linkField . ' = ' . self::STORE_TABLE_ALIAS . '.' . $linkField,
                     []
                 )->group(
                    'main_table.' . $linkField
                );
        }
    }

    /**
     * @param string $tableName
     * @param string $linkField
     * @param string $storeIdField
     */
    protected function addRelatedStoreIds(string $tableName, string $linkField, string $storeIdField): void
    {
        $linkedIds = $this->getColumnValues($linkField);

        if ($linkedIds) {
            $connection = $this->getConnection();
            $select     = $connection->select();
            $select
                ->from($this->getTable($tableName))
                ->where($linkField . ' IN(?)', $linkedIds);

            $groupedStoreIds = $this->groupStoreIdsByTemplateId(
                $connection->fetchAll($select),
                $linkField,
                $storeIdField
            );

            foreach ($this as $templateItem) {
                $templateId = $templateItem->getData($linkField);

                if (empty($groupedStoreIds[$templateId])) {
                    continue;
                }

                $templateItem->setData(
                    \Ecwhim\SeoTemplates\Api\Data\TemplateInterface::STORE_IDS,
                    $groupedStoreIds[$templateId]
                );
            }
        }
    }

    /**
     * @param array $sqlResultRows
     * @param string $templateIdField
     * @param string $storeIdField
     * @return array
     */
    protected function groupStoreIdsByTemplateId(
        array $sqlResultRows,
        string $templateIdField,
        string $storeIdField
    ): array {
        $result = [];

        foreach ($sqlResultRows as $row) {
            $result[$row[$templateIdField]][] = $row[$storeIdField];
        }

        return $result;
    }
}

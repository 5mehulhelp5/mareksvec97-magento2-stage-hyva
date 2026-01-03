<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Model\ResourceModel\Reminder;

use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Store\Model\Store as ModelStore;
use MageWorx\ReviewReminderBase\Api\Data\ReminderInterface;
use MageWorx\ReviewReminderBase\Model\Reminder;

class Collection extends AbstractCollection
{
    const REMINDER_STORE_TABLE = 'mageworx_reviewreminderbase_reminder_store';
    const STORE_TABLE_ALIAS    = 'store_table';
    const STORE_FIELD          = 'store';
    const STORE_TABLE_COLUMN   = 'store_id';

    const REMINDER_CUSTOMER_GROUP_TABLE = 'mageworx_reviewreminderbase_reminder_customer_group';
    const CUSTOMER_GROUP_TABLE_ALIAS    = 'customer_group_table';
    const CUSTOMER_GROUP_FIELD          = 'customer_group';
    const CUSTOMER_GROUP_TABLE_COLUMN   = 'customer_group_id';

    /**
     * ID Field name
     *
     * @var string
     */
    protected $_idFieldName = 'reminder_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'mageworx_reviewreminderbase_reminder_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'reminder_collection';

    /**
     * @var array
     */
    protected $mapFields = [
        self::STORE_FIELD          => self::STORE_TABLE_ALIAS . '.' . self::STORE_TABLE_COLUMN,
        self::CUSTOMER_GROUP_FIELD => self::CUSTOMER_GROUP_TABLE_ALIAS . '.' . self::CUSTOMER_GROUP_TABLE_COLUMN
    ];

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            Reminder::class,
            \MageWorx\ReviewReminderBase\Model\ResourceModel\Reminder::class
        );

        foreach ($this->mapFields as $field => $column) {
            $this->_map['fields'][$field] = $column;
        }
    }

    /**
     * Get SQL for get record count.
     * Extra GROUP BY strip added.
     *
     * @return Select
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(\Magento\Framework\DB\Select::GROUP);

        return $countSelect;
    }

    /**
     * Add field filter to collection
     *
     * @param array|string $field
     * @param int|string|array|null $condition
     * @return AbstractCollection
     */
    public function addFieldToFilter($field, $condition = null): AbstractCollection
    {
        if ($field === self::STORE_TABLE_COLUMN) {
            return $this->addStoreFilter($condition, false);
        }

        if ($field === self::CUSTOMER_GROUP_TABLE_COLUMN) {
            return $this->addCustomerGroupFilter($condition);
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add filter by store
     *
     * @param int|array|ModelStore $store
     * @param bool $withAdmin
     * @return \MageWorx\ReviewReminder\Model\ResourceModel\Reminder\Collection
     */
    public function addStoreFilter($store, $withAdmin = true): Collection
    {
        $this->performAddStoreFilter($store, $withAdmin);

        return $this;
    }

    /**
     * Perform adding filter by store
     *
     * @param int|array|ModelStore $store
     * @param bool $withAdmin
     * @return void
     */
    protected function performAddStoreFilter($store, $withAdmin = true): void
    {
        if ($store instanceof ModelStore) {
            $store = [$store->getId()];
        }

        if (!is_array($store)) {
            $store = [$store];
        }

        if ($withAdmin) {
            $store[] = ModelStore::DEFAULT_STORE_ID;
        }

        $this->addFilter(self::STORE_FIELD, ['in' => $store], 'public');
    }

    /**
     * Perform adding filter by customer group
     *
     * @param int|array $customerGroup
     * @return void
     */
    protected function performAddCustomerGroupFilter($customerGroup): void
    {
        if (!is_array($customerGroup)) {
            $customerGroup = [$customerGroup];
        }

        $this->addFilter(self::CUSTOMER_GROUP_FIELD, ['in' => $customerGroup], 'public');
    }

    /**
     * Add filter by customer group
     *
     * @param int|array $customerGroup
     * @return $this
     */
    public function addCustomerGroupFilter($customerGroup): Collection
    {
        $this->performAddCustomerGroupFilter($customerGroup);

        return $this;
    }


    /**
     * @param string $valueField
     * @param string $labelField
     * @param array $additional
     * @return array
     */
    protected function _toOptionArray($valueField = 'reminder_id', $labelField = 'name', $additional = [])
    {
        return parent::_toOptionArray($valueField, $labelField, $additional);
    }

    /**
     * @param null $limit
     * @param null $offset
     * @return Select
     */
    protected function getAllIdsSelect($limit = null, $offset = null)
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Select::ORDER);
        $idsSelect->reset(Select::LIMIT_COUNT);
        $idsSelect->reset(Select::LIMIT_OFFSET);
        $idsSelect->reset(Select::COLUMNS);
        $idsSelect->columns($this->getResource()->getIdFieldName(), 'main_table');
        $idsSelect->limit($limit, $offset);

        return $idsSelect;
    }

    /**
     * @return void
     */
    public function addAssociatedStoreViews(): void
    {
        $this->updateCollectionItemsUsingAssociatedEntities(
            ReminderInterface::STORE_IDS,
            'mageworx_reviewreminderbase_reminder_store',
            'store_id'
        );
    }

    /**
     * @return void
     */
    public function addAssociatedCustomerGroups(): void
    {
        $this->updateCollectionItemsUsingAssociatedEntities(
            ReminderInterface::CUSTOMER_GROUP_IDS,
            'mageworx_reviewreminderbase_reminder_customer_group',
            'customer_group_id'
        );
    }

    /**
     * @param string $tableName
     * @param string $fieldName
     * @param string $columnName
     */
    protected function updateCollectionItemsUsingAssociatedEntities($fieldName, $tableName, $columnName): void
    {
        $ids = $this->getColumnValues(ReminderInterface::REMINDER_ID);

        if (count($ids)) {
            $data       = [];
            $connection = $this->getConnection();
            $select     = $connection->select()
                                     ->from($this->getTable($tableName))
                                     ->where(ReminderInterface::REMINDER_ID . ' IN (?)', $ids);

            $result = $connection->fetchAll($select);

            if ($result) {
                foreach ($result as $row) {
                    $id                      = $row[ReminderInterface::REMINDER_ID];
                    $data[$id][$fieldName][] = $row[$columnName];
                }
            }

            /**@var Reminder $item * */
            foreach ($this as $item) {
                $itemId = $item->getId();

                if (!empty($data[$itemId])) {
                    $item->setDataUsingMethod($fieldName, $data[$itemId][$fieldName]);
                } else {
                    $item->setDataUsingMethod($fieldName, []);
                }
            }
        }
    }

    /**
     * Perform operations before rendering filters
     *
     * @return void
     */
    protected function _renderFiltersBefore(): void
    {
        $this->joinRelationTable(
            self::REMINDER_STORE_TABLE,
            self::STORE_TABLE_ALIAS,
            self::STORE_FIELD
        );

        $this->joinRelationTable(
            self::REMINDER_CUSTOMER_GROUP_TABLE,
            self::CUSTOMER_GROUP_TABLE_ALIAS,
            self::CUSTOMER_GROUP_FIELD
        );

        parent::_renderFiltersBefore();
    }

    /**
     * Join relation table if there is corresponding filter
     *
     * @param string $tableName
     * @param string $tableAlias
     * @param string $field
     * @param string $linkField
     * @return void
     */
    protected function joinRelationTable(
        $tableName,
        $tableAlias,
        $field,
        $linkField = ReminderInterface::REMINDER_ID
    ): void {

        if ($this->getFilter($field)) {
            $this->getSelect()
                 ->join(
                     [$tableAlias => $this->getTable($tableName)],
                     'main_table.' . $linkField . ' = ' . $tableAlias . '.' . $linkField,
                     []
                 )->group(
                    'main_table.' . $linkField
                );
        }
    }

    /**
     * @return AbstractCollection
     */
    protected function _afterLoad(): AbstractCollection
    {
        $this->addAssociatedStoreViews();
        $this->addAssociatedCustomerGroups();

        return parent::_afterLoad();
    }

    /**
     * @param int $period
     * @param int $customerGroup
     * @param int $storeId
     * @return ReminderInterface|mixed|null
     * @todo to provider class
     */
    public function getItemByParams($period, $customerGroup, $storeId): ?ReminderInterface
    {
        /** @var ReminderInterface $item */
        foreach ($this as $item) {

            if ($period !== $item->getPeriod()) {
                continue;
            }

            if (!in_array($customerGroup, $item->getCustomerGroupIds())) {
                continue;
            }

            if (!in_array('0', $item->getStoreIds()) && !in_array($storeId, $item->getStoreIds())) {
                continue;
            }

            return $item;
        }

        return null;
    }
}

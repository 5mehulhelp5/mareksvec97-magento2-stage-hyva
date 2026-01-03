<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Model\ResourceModel;

use Exception;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use MageWorx\ReviewReminderBase\Api\Data\ReminderInterface;

class Reminder extends AbstractDb
{
    /**
     * Event Manager
     *
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * constructor
     *
     * @param Context $context
     * @param ManagerInterface $eventManager
     * @param mixed $connectionName
     */
    public function __construct(
        Context $context,
        ManagerInterface $eventManager,
        $connectionName = null
    ) {
        $this->eventManager = $eventManager;
        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageworx_reviewreminderbase_reminder', 'reminder_id');
    }

    /**
     * Retrieves Reminder Name from DB by passed id.
     *
     * @param string $id
     * @return string|bool
     */
    public function getReminderNameById($id)
    {
        $adapter = $this->getConnection();
        $select  = $adapter->select()
                           ->from($this->getMainTable(), 'name')
                           ->where('reminder_id = :reminder_id');
        $binds   = ['reminder_id' => (int)$id];

        return $adapter->fetchOne($select, $binds);
    }

    /**
     * @param AbstractModel $object
     * @param array $attribute
     * @return $this
     * @throws Exception
     */
    public function saveAttribute(AbstractModel $object, $attribute): Reminder
    {
        $attributes = is_string($attribute) ? [$attribute] : $attribute;

        if (is_array($attributes) && !empty($attributes)) {
            $this->getConnection()->beginTransaction();
            $data = array_intersect_key($object->getData(), array_flip($attributes));
            try {
                $this->beforeSaveAttribute($object, $attributes);
                if ($object->getId() && !empty($data)) {
                    $this->getConnection()->update(
                        $this->getMainTable(),
                        $data,
                        [$this->getIdFieldName() . '= ?' => (int)$object->getId()]
                    );
                    $object->addData($data);
                }
                $this->afterSaveAttribute($object, $attributes);
                $this->getConnection()->commit();
            } catch (Exception $e) {
                $this->getConnection()->rollBack();
                throw $e;
            }
        }

        return $this;
    }

    /**
     * @param AbstractModel $object
     * @param array $attribute
     * @return $this
     */
    protected function beforeSaveAttribute(AbstractModel $object, $attribute): Reminder
    {
        if ($object->getEventObject() && $object->getEventPrefix()) {
            $this->eventManager->dispatch(
                $object->getEventPrefix() . '_save_attribute_before',
                [
                    $object->getEventObject() => $this,
                    'object'                  => $object,
                    'attribute'               => $attribute
                ]
            );
        }

        return $this;
    }

    /**
     * After save object attribute
     *
     * @param AbstractModel $object
     * @param string $attribute
     * @return $this
     */
    protected function afterSaveAttribute(AbstractModel $object, $attribute): Reminder
    {
        if ($object->getEventObject() && $object->getEventPrefix()) {
            $this->eventManager->dispatch(
                $object->getEventPrefix() . '_save_attribute_after',
                [
                    $object->getEventObject() => $this,
                    'object'                  => $object,
                    'attribute'               => $attribute
                ]
            );
        }

        return $this;
    }

    /**
     * Assign page to store views
     *
     * @param AbstractModel $object
     * @return AbstractDb
     */
    protected function _afterSave(AbstractModel $object): AbstractDb
    {
        $this->assignReminderToStoreViews($object);
        $this->assignReminderToCustomerGroups($object);

        return parent::_afterSave($object);
    }

    /**
     * @param ReminderInterface|AbstractModel $reminder
     */
    protected function assignReminderToStoreViews($reminder): void
    {
        $this->assignEntityToReminder(
            $reminder->getId(),
            $reminder->getStoreIds(),
            'mageworx_reviewreminderbase_reminder_store',
            'store_id'
        );
    }

    /**
     * @param ReminderInterface|AbstractModel $reminder
     */
    protected function assignReminderToCustomerGroups($reminder): void
    {
        $this->assignEntityToReminder(
            $reminder->getId(),
            $reminder->getCustomerGroupIds(),
            'mageworx_reviewreminderbase_reminder_customer_group',
            'customer_group_id'
        );
    }

    /**
     * @param int $reminderId
     * @param array $comingValues
     * @param string $tableName
     * @param string $fieldName
     */
    protected function assignEntityToReminder($reminderId, $comingValues, $tableName, $fieldName): void
    {
        $values = $this->getRelationValues($reminderId, $tableName, $fieldName);

        $table  = $this->getTable($tableName);
        $delete = array_diff($values, $comingValues);
        $insert = array_diff($comingValues, $values);

        if ($delete) {
            $where = [ReminderInterface::REMINDER_ID . '= ?' => $reminderId, $fieldName . ' IN (?)' => $delete];
            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];

            foreach ($insert as $entityId) {
                $data[] = [ReminderInterface::REMINDER_ID => $reminderId, $fieldName => $entityId];
            }
            $this->getConnection()->insertMultiple($table, $data);
        }
    }

    /**
     * Retrieves the values of related entity such as store or customer group for specific reminder.
     *
     * @param int $reminderId
     * @param string $tableName
     * @param string $columnName
     * @return array
     */
    protected function getRelationValues($reminderId, $tableName, $columnName): array
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable($tableName),
            $columnName
        )->where(
            ReminderInterface::REMINDER_ID . ' = ?',
            $reminderId
        );

        return $connection->fetchCol($select);
    }

    /**
     * @param AbstractModel|DataObject $object
     * @return Reminder
     */
    protected function _afterLoad(AbstractModel $object): Reminder
    {
        $this->addAssociatedStoreViews($object);
        $this->addAssociatedCustomerGroups($object);

        return parent::_afterLoad($object);
    }

    /**
     * @param AbstractModel|DataObject $object
     */
    protected function addAssociatedStoreViews(AbstractModel $object): void
    {
        $this->updateObjectUsingAssociatedEntities(
            $object,
            ReminderInterface::STORE_IDS,
            'mageworx_reviewreminderbase_reminder_store',
            'store_id'
        );
    }

    /**
     * @param AbstractModel|DataObject $object
     */
    protected function addAssociatedCustomerGroups(AbstractModel $object): void
    {
        $this->updateObjectUsingAssociatedEntities(
            $object,
            ReminderInterface::CUSTOMER_GROUP_IDS,
            'mageworx_reviewreminderbase_reminder_customer_group',
            'customer_group_id'
        );
    }

    /**
     * @param AbstractModel|DataObject $object
     * @param string $tableName
     * @param string $fieldName
     * @param string $columnName
     * @todo refactor fetchColumn
     */
    protected function updateObjectUsingAssociatedEntities($object, $fieldName, $tableName, $columnName): void
    {
        $id = $object->getId();

        if ($id) {
            $data       = [];
            $connection = $this->getConnection();
            $select     = $connection->select()
                                     ->from($this->getTable($tableName))
                                     ->where(ReminderInterface::REMINDER_ID . ' = ?', $id);

            $result = $connection->fetchAll($select);

            if ($result) {
                foreach ($result as $row) {
                    $data[] = $row[$columnName];
                }
            }

            $object->setDataUsingMethod($fieldName, $data);
        }
    }
}

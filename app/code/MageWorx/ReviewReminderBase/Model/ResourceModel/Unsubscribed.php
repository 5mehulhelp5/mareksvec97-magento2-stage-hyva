<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Model\ResourceModel;

use Exception;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class Unsubscribed extends AbstractDb
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
        $this->_init('mageworx_reviewreminderbase_unsubscribed', 'unsubscribed_id');
    }

    /**
     * Retrieves Unsubscribed Email from DB by passed id.
     *
     * @param string $id
     * @return string|bool
     */
    public function getUnsubscribedEmailById($id)
    {
        $adapter = $this->getConnection();
        $select  = $adapter->select()
                           ->from($this->getMainTable(), 'email')
                           ->where('unsubscribed_id = :unsubscribed_id');
        $binds   = ['unsubscribed_id' => (int)$id];

        return $adapter->fetchOne($select, $binds);
    }

    /**
     * @param AbstractModel $object
     * @param array $attribute
     * @return $this
     * @throws Exception
     */
    public function saveAttribute(AbstractModel $object, $attribute): Unsubscribed
    {
        if (is_string($attribute)) {
            $attributes = [$attribute];
        } else {
            $attributes = $attribute;
        }
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
    protected function beforeSaveAttribute(AbstractModel $object, $attribute): Unsubscribed
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
    protected function afterSaveAttribute(AbstractModel $object, $attribute): Unsubscribed
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
}

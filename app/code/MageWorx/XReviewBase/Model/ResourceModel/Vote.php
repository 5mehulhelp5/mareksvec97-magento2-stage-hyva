<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XReviewBase\Model\ResourceModel;

use Exception;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class Vote extends AbstractDb
{
    /**
     * Event Manager
     *
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * Vote constructor.
     *
     * @param Context $context
     * @param ManagerInterface $eventManager
     * @param null $connectionName
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
        $this->_init('mageworx_xreviewbase_review_vote', 'vote_id');
    }

    /**
     * @param int $reviewId
     * @return array|false
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getOverallVotes($reviewId)
    {
        $select = $this->getConnection()->select();

        $select
            ->from(
                $this->getMainTable(),
                [
                    'like_count' => new \Zend_Db_Expr('IFNULL(SUM(like_count), 0)'),
                    'dislike_count' => new \Zend_Db_Expr('IFNULL(SUM(dislike_count), 0)')
                ]
            )
            ->where('review_id = :review_id');

        return $this->getConnection()->fetchRow($select, ['review_id' => (int)$reviewId]);
    }

    /**
     * @param int $reviewId
     * @param string $ip
     * @return array|false
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPersonalVotes($reviewId, $ip)
    {
        $select = $this->getConnection()->select();

        $select
            ->from(
                $this->getMainTable(),
                [
                    'like_count' => new \Zend_Db_Expr('IFNULL(SUM(like_count), 0)'),
                    'dislike_count' => new \Zend_Db_Expr('IFNULL(SUM(dislike_count), 0)')
                ]
            )
            ->where('review_id = :review_id')
            ->where('ip_address = :ip_address');

        return $this->getConnection()->fetchRow($select, ['review_id' => (int)$reviewId, 'ip_address'=> $ip]);
    }

    /**
     * @param AbstractModel $object
     * @param array $attribute
     * @return $this
     * @throws Exception
     */
    public function saveAttribute(AbstractModel $object, $attribute)
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
    protected function beforeSaveAttribute(AbstractModel $object, $attribute)
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
    protected function afterSaveAttribute(AbstractModel $object, $attribute)
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

<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XReviewBase\Model\ResourceModel;

use Magento\Review\Model\ResourceModel\Rating;
use MageWorx\XReviewBase\Model\Review\AdditionalDetailsFields\Config as FieldsConfig;

class Review extends \Magento\Review\Model\ResourceModel\Review
{
    /**
     * @var FieldsConfig
     */
    protected $fieldsConfig;

    /**
     * Review constructor.
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Review\Model\RatingFactory $ratingFactory
     * @param Rating\Option $ratingOptions
     * @param FieldsConfig $fieldsConfig
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Review\Model\RatingFactory $ratingFactory,
        Rating\Option $ratingOptions,
        FieldsConfig $fieldsConfig,
        $connectionName = null
    ) {
        parent::__construct($context, $date, $storeManager, $ratingFactory, $ratingOptions, $connectionName);
        $this->fieldsConfig = $fieldsConfig;
    }

    /**
     * @param \Magento\XReviewBase\Model\Review $review
     */
    public function saveReviewDetailFields(\Magento\Review\Model\Review $review): void
    {
        $fields = $this->fieldsConfig->getFieldsForReviewDetail();

        $detail = [];

        foreach ($fields as $field) {
            $detail[$field] = $review->getData($field);
        }

        $connection = $this->getConnection();

        $select = $connection->select()
                             ->from(
                                 $this->getTable('review_detail'),
                                 'detail_id'
                             )
                             ->where('review_id = :review_id');

        $detailId = $connection->fetchOne($select, [':review_id' => $review->getId()]);

        if ($detailId) {
            $condition = ["detail_id = ?" => $detailId];
            $connection->update($this->getTable('review_detail'), $detail, $condition);
        }
    }

    /**
     * @param \Magento\Review\Model\ResourceModel\Review\Collection $collection
     * @return \Magento\Review\Model\ResourceModel\Review\Collection
     */
    public function addImageCountToReviewCollection($collection)
    {
        $this->joinMedia($collection, 'main_table', 'joinLeft', true);
        $collection->getSelect()->group("main_table.review_id");

        return $collection;
    }

    /**
     * @param \Magento\Review\Model\ResourceModel\Review\Collection $collection
     * @return \Magento\Review\Model\ResourceModel\Review\Collection
     */
    public function addMediaExistsFilterToReviewCollection($collection)
    {
        $this->joinMedia($collection, 'main_table', 'join', true);
        $collection->getSelect()->group("main_table.review_id");

        return $collection;
    }

    /**
     * @param \Magento\Review\Model\ResourceModel\Review\Collection $collection
     * @param string $mainTable
     * @param string $joinType join or joinLeft
     * @param bool $withCount
     */
    protected function joinMedia($collection, $mainTable, $joinType = 'join', $withCount = true)
    {
        if (!$collection->getFlag('mageworx_media_joined')) {
            $select = $collection->getSelect();

            $fields = [];

            if ($withCount) {
                $fields['image_count'] = new \Zend_Db_Expr('COUNT(DISTINCT media_table.review_id)');
            }

            $select->$joinType(
                ['media_table' => $this->getTable('mageworx_xreviewbase_review_media')],
                $this->getConnection()->quoteIdentifier($mainTable) . '.review_id=media_table.review_id AND media_table.disabled=0',
                $fields
            );

            $collection->setFlag('mageworx_media_joined', true);
        }
    }

    /**
     * @param int $entityPkValue
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductRatingStatistics($entityPkValue)
    {
        $subSelect = $this->getSubQuery($entityPkValue);

        $bind = [
            ':pk_value'  => $entityPkValue,
            ':status_id' => \Magento\Review\Model\Review::STATUS_APPROVED,
            ':entity_id' => $this->getEntityIdByCode(\Magento\Review\Model\Review::ENTITY_PRODUCT_CODE)
        ];

        $bind[':store_id'] = (int)$this->_storeManager->getStore()->getId();

        $connection = $this->getConnection();

        $select = $connection->select()->from(
            ['rating_stage_table' => $subSelect],
            [
                'rating_stage_table.stage_value',
                'stage_count'   => new \Zend_Db_Expr('COUNT(rating_stage_table.stage_value)'),
                'stage_reviews' => new \Zend_Db_Expr(
                    'CONCAT("/",GROUP_CONCAT(rating_stage_table.review_id separator "/"),"/")'
                )
            ]
        );

        $select->group('rating_stage_table.stage_value')->order('rating_stage_table.stage_value');

        return $connection->fetchAll($select);
     }

    /**
     * @param int $entityPkValue
     * @return \Magento\Framework\DB\Select
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSubQuery($entityPkValue)
    {
        $connection = $this->getConnection();
        $select     = $connection->select()->from(
            $this->_reviewTable,
            ['review_id']
        )->where(
            $connection->quoteInto("{$this->_reviewTable}.entity_pk_value = ?", (int)$entityPkValue)
        )->where(
            $connection->quoteInto("{$this->_reviewTable}.status_id = ?", \Magento\Review\Model\Review::STATUS_APPROVED)
        )->where(
            $connection->quoteInto(
                "{$this->_reviewTable}.entity_id = ?",
                $this->getEntityIdByCode(\Magento\Review\Model\Review::ENTITY_PRODUCT_CODE)
            )
        );

        $select->join(
            ['store' => $this->_reviewStoreTable],
            $connection->quoteInto(
                $this->_reviewTable . '.review_id=store.review_id AND store.store_id = ?',
                (int)$this->_storeManager->getStore()->getId()
            ),
            []
        );

        $this->joinRatingVote($select, $this->getTable($this->_mainTable), true);
        $select->group("{$this->_reviewTable}.review_id");

        return $select;
    }

    /**
     * @param \Magento\Review\Model\ResourceModel\Review\Collection $collection
     */
    public function addAverageCustomerRatingToReviewCollection($collection)
    {
        $select = $collection->getSelect();
        $this->joinRatingVote($select,  'main_table', false);
        $select->group('main_table.review_id');
    }

    /**
     * @param \Magento\Framework\Db\Select $select
     * @param string $tableName
     * @param bool $withCount
     */
    protected function joinRatingVote($select, $tableName, $withCount = false)
    {
        $fields = [
            'stage_value'  => new \Zend_Db_Expr('FLOOR(AVG(v.percent)/20)')
        ];

        if ($withCount) {
            $fields['rating_count'] = new \Zend_Db_Expr('COUNT(v.rating_id) ');
        }

        $select->joinLeft(
            ['v' => $this->getTable('rating_option_vote')],
            $tableName . '.review_id=v.review_id',
            $fields
        );
    }
}

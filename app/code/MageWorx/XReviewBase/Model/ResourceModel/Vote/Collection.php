<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XReviewBase\Model\ResourceModel\Vote;

use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * ID Field name
     *
     * @var string
     */
    protected $_idFieldName = 'vote_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'mageworx_xreviewbase_review_vote_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'review_vote_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \MageWorx\XReviewBase\Model\Vote::class,
            \MageWorx\XReviewBase\Model\ResourceModel\Vote::class
        );
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
     * @param string $valueField
     * @param string $labelField
     * @param array $additional
     * @return array
     */
    protected function _toOptionArray(
        $valueField = 'value_id',
        $labelField = 'review_id',
        $additional = ['like_count', 'dislike_count']
    ) {
        return parent::_toOptionArray($valueField, $labelField, $additional);
    }

    /**
     * @param null $limit
     * @param null $offset
     * @return Select
     * @throws \Magento\Framework\Exception\LocalizedException
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
}

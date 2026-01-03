<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XReviewBase\Model\ResourceModel\Media;

use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * ID Field name
     *
     * @var string
     */
    protected $_idFieldName = 'value_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'mageworx_xreviewbase_media_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'media_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \MageWorx\XReviewBase\Model\Media::class,
            \MageWorx\XReviewBase\Model\ResourceModel\Media::class
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
    protected function _toOptionArray($valueField = 'value_id', $labelField = 'media_url', $additional = [])
    {
        return parent::_toOptionArray($valueField, $labelField, $additional);
    }
}

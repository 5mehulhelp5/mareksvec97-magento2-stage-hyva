<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XReviewBase\Plugin\ReviewCollection;

use MageWorx\XReviewBase\Model\Review\AdditionalDetailsFields\Config as AdditionalFieldsConfig;

class ExtendFilterPlugin
{
    /**
     * @var AdditionalFieldsConfig
     */
    protected $additionalDetailsFieldsConfig;

    /**
     * ExtendFilterPlugin constructor.
     *
     * @param AdditionalFieldsConfig $additionalDetailsFieldsConfig
     */
    public function __construct(
        AdditionalFieldsConfig $additionalDetailsFieldsConfig
    ) {
        $this->additionalDetailsFieldsConfig = $additionalDetailsFieldsConfig;
    }

    /**
     * @param \Magento\Review\Model\ResourceModel\Review\Product\Collection $subject
     * @return array
     */
    public function aroundAddAttributeToFilter($subject, callable $proceed, ...$args)
    {
        $fields = [];

        foreach ($this->additionalDetailsFieldsConfig->getFieldsForReviewDetail() as $field) {
            $fields[] = 'detail.' . $field;
        }

        if (in_array($args[0], $fields)) {
            $conditionSql = $subject->getConnection()->prepareSqlCondition($args[0], $args[1]);
            $subject->getSelect()->where($conditionSql);
        } else {
            $proceed(...$args);
        }
    }
}

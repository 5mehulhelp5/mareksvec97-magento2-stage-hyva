<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\XReviewBase\Model\Review\AdditionalDetailsFields;

class Config
{
    /**
     * Retrieves list of all additional fields for review_detail table
     *
     * @return array
     */
    public function getFieldsForReviewDetail(): array
    {
        return [
            'location',
            'region',
            'answer',
            'is_recommend',
            'is_verified',
            'pros',
            'cons'
        ];
    }

    /**
     * Retrieves list of fields which can't be saved by customer
     *
     * @return array
     */
    public function getFrontendDiscardFieldsForReviewDetail(): array
    {
        return [
            'location',
            'region',
            'is_verified',
            'answer'
        ];
    }
}

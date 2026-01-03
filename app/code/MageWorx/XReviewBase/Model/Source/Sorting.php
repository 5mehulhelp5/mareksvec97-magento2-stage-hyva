<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\XReviewBase\Model\Source;

class Sorting extends \MageWorx\XReviewBase\Model\Source
{
    const SORT_FIELD_CREATED_AT  = 'created_at';
    const SORT_FIELD_STAGE_VALUE = 'stage_value';

    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::SORT_FIELD_CREATED_AT,
                'label' => __('Date')
            ],
            [
                'value' => self::SORT_FIELD_STAGE_VALUE,
                'label' => __('Rating')
            ]
        ];
    }
}

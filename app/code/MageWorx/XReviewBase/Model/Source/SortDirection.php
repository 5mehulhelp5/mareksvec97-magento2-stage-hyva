<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\XReviewBase\Model\Source;

class SortDirection extends \MageWorx\XReviewBase\Model\Source
{
    const SORT_ASC  = 'asc';
    const SORT_DESC = 'desc';

    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::SORT_ASC,
                'label' => __('Sort Ascending')
            ],
            [
                'value' => self::SORT_DESC,
                'label' => __('Sort Descending')
            ]
        ];
    }
}

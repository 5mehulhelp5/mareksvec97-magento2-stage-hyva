<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\XReviewBase\Model\Source;

class Filter extends \MageWorx\XReviewBase\Model\Source
{
    const FILTER_BY_VERIFIED_CUSTOMER = 'customer';
    const FILTER_BY_MEDIA             = 'media';
    const FILTER_BY_LOCATION          = 'location';

    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::FILTER_BY_LOCATION,
                'label' => __('Your Country')
            ],
            [
                'value' => self::FILTER_BY_MEDIA,
                'label' => __('With images')
            ],
            [
                'value' => self::FILTER_BY_VERIFIED_CUSTOMER,
                'label' => __('Verified customers')
            ]
        ];
    }
}

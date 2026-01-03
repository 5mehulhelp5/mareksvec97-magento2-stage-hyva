<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Model\Reminder\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{
    const DISABLE = 0;
    const ENABLE  = 1;

    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $options = [
            [
                'value' => self::DISABLE,
                'label' => __('Disable')
            ],
            [
                'value' => self::ENABLE,
                'label' => __('Enable')
            ]
        ];

        return $options;
    }
}

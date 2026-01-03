<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Model\Reminder\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Type implements OptionSourceInterface
{
    const TYPE_EMAIL = 'email';
    const TYPE_POPUP = 'popup';

    /**
     * to option array
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $options = [
            [
                'value' => self::TYPE_EMAIL,
                'label' => __('Email')
            ],
            [
                'value' => self::TYPE_POPUP,
                'label' => __('Popup')
            ],
        ];

        return $options;
    }
}

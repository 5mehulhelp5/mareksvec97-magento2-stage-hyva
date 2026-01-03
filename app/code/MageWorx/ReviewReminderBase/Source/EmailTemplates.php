<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Source;

use Magento\Config\Model\Config\Source\Email\Template;
use MageWorx\ReviewReminderBase\Model\ReminderConfigReader;

class EmailTemplates extends Template
{
    const BY_CONFIG_VALUE = '0';

    /**
     * {@inheritdoc}
     */
    public function toOptionArray(): array
    {
        $options = parent::toOptionArray();
        array_unshift($options, ['value' => self::BY_CONFIG_VALUE, 'label' => __('Use config')]);

        return $options;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return ReminderConfigReader::CONFIG_PATH_EMAIL_TEMPLATE;
    }
}

<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class BreadcrumbRenderStyles implements OptionSourceInterface
{
    public const CLASSIC = 'classic';
    public const PROGRESS_BAR = 'progress_bar';

    /**
     * @return array<int, array<string, string>>
     */
    public function toOptionArray(): array
    {
        return [
            [
                'label' => 'Classic',
                'value' => self::CLASSIC,
            ],
            [
                'label' => 'Progress Bar',
                'value' => self::PROGRESS_BAR
            ],
        ];
    }
}

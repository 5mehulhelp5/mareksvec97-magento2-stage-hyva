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

class TooltipRenderStyles implements OptionSourceInterface
{
    public const CLASSIC = 'classic';
    public const HINT_TEXT = 'hint_text';

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
                'label' => 'Hint Text',
                'value' => self::HINT_TEXT
            ],
        ];
    }
}

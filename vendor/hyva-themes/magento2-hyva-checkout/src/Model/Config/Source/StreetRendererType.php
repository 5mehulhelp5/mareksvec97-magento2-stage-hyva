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

class StreetRendererType implements OptionSourceInterface
{
    public const OPTION_TWO_COLUMN_GRID = 1;
    public const OPTION_ONE_COLUMN_ROW = 2;

    /**
     * @return array[]
     */
    public function toOptionArray(): array
    {
        return [
            [
                'label' => 'Default',
                'value' => 0
            ],
            [
                'label' => 'Two column grid',
                'value' => self::OPTION_TWO_COLUMN_GRID
            ],
            [
                'label' => 'One Column Row',
                'value' => self::OPTION_ONE_COLUMN_ROW
            ]
        ];
    }
}

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

class AddressRendererType implements OptionSourceInterface
{
    /**
     * @return array[]
     */
    public function toOptionArray(): array
    {
        return [
            [
                'label' => 'Grid',
                'value' => 'grid'
            ],
            [
                'label' => 'List',
                'value' => 'list'
            ],
            [
                'label' => 'Select',
                'value' => 'select'
            ]
        ];
    }
}

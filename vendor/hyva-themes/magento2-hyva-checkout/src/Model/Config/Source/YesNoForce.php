<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Config\Source;

use Magento\Config\Model\Config\Source\Yesno;

class YesNoForce extends Yesno
{
    public function toOptionArray(): array
    {
        return array_merge(parent::toOptionArray(), [['value' => 2, 'label' => __('Force')]]);
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [2 => __('No')]);
    }
}

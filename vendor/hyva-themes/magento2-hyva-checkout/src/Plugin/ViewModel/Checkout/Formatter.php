<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Plugin\ViewModel\Checkout;

use Hyva\Checkout\Model\ConfigData\HyvaThemes\SystemConfigDesign;
use Hyva\Checkout\ViewModel\Checkout\Formatter as Subject;
use Magento\Framework\Phrase;

class Formatter
{
    public function __construct(
        private readonly SystemConfigDesign $systemConfigDesign
    ) {
        //
    }

    public function afterFilterCurrencyConditions(Subject $subject, array $conditions): array
    {
        $showZeroPriceFreeLabel = $this->systemConfigDesign->formatting()->showZeroPriceFreeLabel();

        if ($showZeroPriceFreeLabel && ! is_callable($conditions['free'] ?? null)) {
            $conditions['free'] = fn(float $amount): Phrase|float => $amount === 0.00 ? __('Free') : $amount;
        }

        return $conditions;
    }
}

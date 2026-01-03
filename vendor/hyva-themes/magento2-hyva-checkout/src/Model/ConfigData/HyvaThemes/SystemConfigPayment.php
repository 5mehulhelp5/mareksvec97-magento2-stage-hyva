<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\ConfigData\HyvaThemes;

class SystemConfigPayment
{
    protected SystemConfigComponents $systemConfigComponents;

    public function __construct(
        SystemConfigComponents $systemConfigComponents
    ) {
        $this->systemConfigComponents = $systemConfigComponents;
    }

    public function canDisplayMethodIcons(): bool
    {
        return $this->systemConfigComponents->isSetFlagForComponent('show_method_icons', 'payment');
    }

    public function removeNonZeroPaymentMethods(): bool
    {
        return $this->systemConfigComponents->isSetFlagForComponent('remove_non_zero_methods', 'payment');
    }

    public function getEnabledZeroPaymentMethods(): array
    {
        return explode(',', $this->systemConfigComponents->getComponentValue('enabled_non_zero_methods', 'payment'));
    }
}

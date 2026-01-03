<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\ConfigData\HyvaThemes;

class SystemConfigGuestDetails
{
    protected SystemConfigComponents $systemConfigComponents;

    public function __construct(
        SystemConfigComponents $systemConfigComponents
    ) {
        $this->systemConfigComponents = $systemConfigComponents;
    }

    public function getEmailAddressTooltip(): string
    {
        return $this->systemConfigComponents->getComponentValue('email_address_tooltip', 'guest_details')
            ?? ($this->systemConfigComponents->isSetFlagForComponent('use_luma_checkout_email_tooltip', 'shipping_address')
                ? (string) __('We\'ll send your order confirmation here.') : '');
    }

    public function enableLogin(): bool
    {
        return $this->systemConfigComponents->isSetFlagForComponent('enable_login', 'guest_details');
    }
}

<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\ConfigData\HyvaThemes;

class SystemConfigAddressForm
{
    protected SystemConfigComponents $systemConfigComponents;

    public function __construct(
        SystemConfigComponents $systemConfigComponents
    ) {
        $this->systemConfigComponents = $systemConfigComponents;
    }

    /**
     * @deprecated method has been replaced by SystemConfigGuestDetails::getEmailAddressTooltip.
     * @see \Hyva\Checkout\Model\ConfigData\HyvaThemes\SystemConfigGuestDetails
     */
    public function useLumaShippingAddressEmailTooltip(): bool
    {
        return $this->systemConfigComponents->isSetFlagForComponent('use_luma_checkout_email_tooltip', 'shipping_address');
    }

    public function useLumaShippingAddressTelephoneTooltip(): bool
    {
        return $this->systemConfigComponents->isSetFlagForComponent('use_luma_checkout_telephone_tooltip', 'shipping_address');
    }
}

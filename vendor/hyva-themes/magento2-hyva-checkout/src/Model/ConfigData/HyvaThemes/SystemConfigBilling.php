<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\ConfigData\HyvaThemes;

class SystemConfigBilling
{
    protected SystemConfigComponents $systemConfigComponents;

    public function __construct(
        SystemConfigComponents $systemConfigComponents
    ) {
        $this->systemConfigComponents = $systemConfigComponents;
    }

    public function canApplyShippingAsBillingAddress(): bool
    {
        return $this->systemConfigComponents->isSetFlagForComponent('shipping_as_billing', 'billing');
    }

    public function getAddressListView(): string
    {
        return $this->systemConfigComponents->getComponentValue('address_list_view', 'billing');
    }
}

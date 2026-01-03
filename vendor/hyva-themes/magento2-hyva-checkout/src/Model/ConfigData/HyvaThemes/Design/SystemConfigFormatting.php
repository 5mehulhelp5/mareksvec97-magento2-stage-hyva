<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\ConfigData\HyvaThemes\Design;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class SystemConfigFormatting
{
    public const XML_PATH_SHOW_ZERO_PRICE_FREE_LABEL = 'hyva_themes_checkout/design/formatting/show_zero_price_free_label';
    public const XML_PATH_SHOW_SHIPPING_METHOD_TOTAL_IF_NONE_SELECTED = 'hyva_themes_checkout/design/formatting/show_shipping_method_total_if_none_selected';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
        //
    }

    public function showZeroPriceFreeLabel(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_SHOW_ZERO_PRICE_FREE_LABEL, ScopeInterface::SCOPE_STORE);
    }

    public function showShippingMethodTotalIfNoneSelected(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_SHOW_SHIPPING_METHOD_TOTAL_IF_NONE_SELECTED, ScopeInterface::SCOPE_STORE);
    }
}

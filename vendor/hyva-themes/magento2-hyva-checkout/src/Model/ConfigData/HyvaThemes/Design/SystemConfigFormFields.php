<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\ConfigData\HyvaThemes\Design;

use Hyva\Checkout\Model\Config\Source\TooltipRenderStyles;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class SystemConfigFormFields
{
    public const XML_PATH_SHOW_PASSWORD_VISIBILITY_TOGGLE = 'hyva_themes_checkout/design/form_fields/show_password_visibility_toggle';
    public const XML_PATH_TOOLTIP_STYLE = 'hyva_themes_checkout/design/form_fields/tooltip_style';

    protected ScopeConfigInterface $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function showPasswordVisibilityToggle(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_SHOW_PASSWORD_VISIBILITY_TOGGLE, ScopeInterface::SCOPE_STORE);
    }

    public function getTooltipStyle(): string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_TOOLTIP_STYLE, ScopeInterface::SCOPE_STORE) ?? TooltipRenderStyles::CLASSIC;
    }

    public function showTooltipAs(string $style): bool
    {
        return strtolower($style) == $this->getTooltipStyle();
    }

    public function showTooltipAsClassic(): bool
    {
        return $this->showTooltipAs(TooltipRenderStyles::CLASSIC);
    }

    public function showTooltipAsHintText(): bool
    {
        return $this->showTooltipAs(TooltipRenderStyles::HINT_TEXT);
    }
}

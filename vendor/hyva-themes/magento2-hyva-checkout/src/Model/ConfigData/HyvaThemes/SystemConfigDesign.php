<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\ConfigData\HyvaThemes;

use Hyva\Checkout\Model\ConfigData\HyvaThemes\Design\SystemConfigFormatting;
use Hyva\Checkout\Model\ConfigData\HyvaThemes\Design\SystemConfigFormFields;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\ScopeInterface;

class SystemConfigDesign
{
    public const XML_PATH_UNIVERSAL_ICON_WIDTH = 'hyva_themes_checkout/design/shipping_payment_methods/universal_icon_width';
    public const XML_PATH_UNIVERSAL_ICON_HEIGHT = 'hyva_themes_checkout/design/shipping_payment_methods/universal_icon_height';
    public const XML_PATH_COMBINE_SHIPPING_METHOD_NAME = 'hyva_themes_checkout/design/shipping_payment_methods/combine_shipping_method_name';

    protected ScopeConfigInterface $scopeConfig;
    protected SystemConfigFormFields $systemConfigFormFields;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ?SystemConfigFormFields $systemConfigFormFields = null,
        private ?SystemConfigFormatting $systemConfigFormatting = null
    ) {
        $this->scopeConfig = $scopeConfig;

        $this->systemConfigFormFields ??= ObjectManager::getInstance()->get(SystemConfigFormFields::class);
        $this->systemConfigFormatting ??= ObjectManager::getInstance()->get(SystemConfigFormatting::class);
    }

    public function getUniversalIconWidth(): int
    {
        return (int) $this->scopeConfig->getValue(self::XML_PATH_UNIVERSAL_ICON_WIDTH, ScopeInterface::SCOPE_STORE) ?? 44;
    }

    public function getUniversalIconHeight(): int
    {
        return (int) $this->scopeConfig->getValue(self::XML_PATH_UNIVERSAL_ICON_HEIGHT, ScopeInterface::SCOPE_STORE) ?? 44;
    }

    public function formFields(): SystemConfigFormFields
    {
        return $this->systemConfigFormFields;
    }

    public function formatting(): SystemConfigFormatting
    {
        return $this->systemConfigFormatting;
    }

    public function canCombineShippingMethodName(): bool
    {
        return (bool) $this->scopeConfig->getValue(self::XML_PATH_COMBINE_SHIPPING_METHOD_NAME, ScopeInterface::SCOPE_STORE) ?? false;
    }
}

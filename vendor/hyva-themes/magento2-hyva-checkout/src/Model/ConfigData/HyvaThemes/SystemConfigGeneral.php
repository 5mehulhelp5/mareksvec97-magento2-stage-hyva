<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\ConfigData\HyvaThemes;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class SystemConfigGeneral
{
    public const XML_PATH_GENERAL_CHECKOUT = 'hyva_themes_checkout/general/checkout';
    public const XML_PATH_GENERAL_MOBILE_ENABLE = 'hyva_themes_checkout/general/mobile/enable';
    public const XML_PATH_GENERAL_MOBILE_CHECKOUT = 'hyva_themes_checkout/general/mobile/checkout';

    protected ScopeConfigInterface $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function getCheckout(): string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_CHECKOUT, ScopeInterface::SCOPE_STORE) ?? 'default';
    }

    public function hasMobileCheckout(): bool
    {
        $desktopCheckout = $this->getCheckout();
        $mobileCheckout = $this->getMobileCheckout();

        $enabled = $this->scopeConfig->isSetFlag(self::XML_PATH_GENERAL_MOBILE_ENABLE, ScopeInterface::SCOPE_STORE);
        return $enabled === true && ! empty($mobileCheckout) && $desktopCheckout !== $mobileCheckout;
    }

    public function getMobileCheckout(): string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_MOBILE_CHECKOUT, ScopeInterface::SCOPE_STORE) ?? $this->getCheckout();
    }
}

<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\ConfigData\HyvaThemes\Developer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class SystemConfigAddressForms
{
    public const XML_PATH_RENDERER_TYPE = 'hyva_themes_checkout/developer/address_form/use_street_renderer';

    protected ScopeConfigInterface $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function getRendererType(): int
    {
        return (int) $this->scopeConfig->getValue(self::XML_PATH_RENDERER_TYPE, ScopeInterface::SCOPE_STORE);
    }
}

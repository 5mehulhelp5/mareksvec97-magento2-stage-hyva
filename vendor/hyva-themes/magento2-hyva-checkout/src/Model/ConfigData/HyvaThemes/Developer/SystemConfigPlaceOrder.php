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

class SystemConfigPlaceOrder
{
    public const XML_PATH_SUCCESS_PAGE_REDIRECT_TIMEOUT = 'hyva_themes_checkout/developer/place_order/success_page_redirect_timeout';

    protected ScopeConfigInterface $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function getSuccessPageRedirectTimeout(): int
    {
        return (int) $this->scopeConfig->getValue(self::XML_PATH_SUCCESS_PAGE_REDIRECT_TIMEOUT, ScopeInterface::SCOPE_STORE) ?? 3000;
    }
}

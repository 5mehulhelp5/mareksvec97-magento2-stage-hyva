<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\ConfigData\HyvaThemes\Developer;

use Magento\Customer\Model\Session as SessionCustomer;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class SystemConfigFixesWorkarounds
{
    public const XML_PATH_MIGRATE_GUEST_INFO = 'hyva_themes_checkout/developer/fixes_workarounds/migrate_guest_info';
    public const XML_PATH_TOTAL_SEGMENTS_RECOLLECT = 'hyva_themes_checkout/developer/fixes_workarounds/total_segments_collect_totals';
    public const XML_PATH_RENDER_BILLING_ADDRESS = 'hyva_themes_checkout/developer/fixes_workarounds/render_billing_address';
    public const XML_PATH_DISABLE_FORM_AUTO_SAVING = 'hyva_themes_checkout/developer/fixes_workarounds/disable_form_auto_saving';

    private ScopeConfigInterface $scopeConfig;
    private SessionCustomer $sessionCustomer;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        SessionCustomer $sessionCustomer
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->sessionCustomer = $sessionCustomer;
    }

    public function applyMigrateGuestInfo(bool $checkLogin = true): bool
    {
        if ($checkLogin && $this->sessionCustomer->isLoggedIn()) {
            return false;
        }

        return $this->scopeConfig->isSetFlag(self::XML_PATH_MIGRATE_GUEST_INFO, ScopeInterface::SCOPE_STORE) ?? true;
    }

    public function reCollectTotalsInTotalSegments($scopeId = null): bool
    {
        return (bool)$this->scopeConfig->isSetFlag(
            self::XML_PATH_TOTAL_SEGMENTS_RECOLLECT,
            ScopeInterface::SCOPE_STORE,
            $scopeId
        );
    }

    public function canRenderBillingAddress(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_RENDER_BILLING_ADDRESS, ScopeInterface::SCOPE_STORE) ?? false;
    }

    public function disableFormAutoSaving(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_DISABLE_FORM_AUTO_SAVING, ScopeInterface::SCOPE_STORE) ?? false;
    }
}

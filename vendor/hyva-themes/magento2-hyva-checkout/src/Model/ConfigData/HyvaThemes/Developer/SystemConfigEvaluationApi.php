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

class SystemConfigEvaluationApi
{
    public const XML_PATH_ERROR_AS_WARNING = 'hyva_themes_checkout/developer/evaluation_api/error_as_warning';

    // Redirect result xml paths.
    public const XML_PATH_ENABLE_CONFIRMATION = 'hyva_themes_checkout/developer/evaluation_api/redirect/enable_confirmation';
    public const XML_PATH_CONFIRMATION_MESSAGE = 'hyva_themes_checkout/developer/evaluation_type/redirect/confirmation_message';
    public const XML_PATH_ENABLE_NOTIFICATION = 'hyva_themes_checkout/developer/evaluation_api/redirect/enable_notification';
    public const XML_PATH_NOTIFICATION_MESSAGE = 'hyva_themes_checkout/developer/evaluation_api/redirect/notification_message';
    public const XML_PATH_DIALOG_TIMEOUT = 'hyva_themes_checkout/developer/evaluation_api/redirect/dialog_timeout';

    protected ScopeConfigInterface $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function displayErrorAsWarning(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ERROR_AS_WARNING, ScopeInterface::SCOPE_STORE);
    }

    public function canShowConfirmationDialog(): bool
    {
        return (int) $this->scopeConfig->getValue(
            self::XML_PATH_ENABLE_CONFIRMATION,
            ScopeInterface::SCOPE_STORE
        ) === 1;
    }

    public function canForceConfirmationDialog(): bool
    {
        return (int) $this->scopeConfig->getValue(
            self::XML_PATH_ENABLE_CONFIRMATION,
            ScopeInterface::SCOPE_STORE
        ) === 2;
    }

    public function getConfirmationMessage(): string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CONFIRMATION_MESSAGE,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function canShowNotificationDialog(): bool
    {
        return (int) $this->scopeConfig->getValue(
            self::XML_PATH_ENABLE_NOTIFICATION,
            ScopeInterface::SCOPE_STORE
        ) === 1;
    }

    public function getNotificationMessage(): string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NOTIFICATION_MESSAGE,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function canForceNotificationDialog(): bool
    {
        return (int) $this->scopeConfig->getValue(
            self::XML_PATH_ENABLE_NOTIFICATION,
            ScopeInterface::SCOPE_STORE
        ) === 2;
    }

    public function getVisibilityDuration(): int
    {
        $timeout = (int) $this->scopeConfig->getValue(
            self::XML_PATH_DIALOG_TIMEOUT,
            ScopeInterface::SCOPE_STORE
        ) ?? null;

        return $timeout === 0 ? 3000 : $timeout;
    }
}

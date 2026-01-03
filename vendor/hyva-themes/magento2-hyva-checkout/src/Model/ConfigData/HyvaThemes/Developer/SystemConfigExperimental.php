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

class SystemConfigExperimental
{
    public const XML_PATH_ENABLE_FIRST_PAYMENT_METHOD_DEFAULT = 'hyva_themes_checkout/developer/experimental/enable_first_payment_method_available';
    public const XML_PATH_ENABLE_FIRST_SHIPPING_METHOD_DEFAULT = 'hyva_themes_checkout/developer/experimental/enable_first_shipping_method_available';
    public const XML_PATH_DISABLE_MAIN_EVALUATION_RESULT_MERGE = 'hyva_themes_checkout/developer/experimental/disable_main_evaluation_result_merge';

    protected ScopeConfigInterface $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function enableFirstAvailablePaymentMethod(): bool
    {
        return (bool) $this->scopeConfig->getValue(self::XML_PATH_ENABLE_FIRST_PAYMENT_METHOD_DEFAULT, ScopeInterface::SCOPE_STORE);
    }

    public function enableFirstAvailableShippingMethod(): bool
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_ENABLE_FIRST_SHIPPING_METHOD_DEFAULT, ScopeInterface::SCOPE_STORE);
    }

    public function disableMainEvaluationResultMerge(): bool
    {
        return (bool) $this->scopeConfig->getValue(self::XML_PATH_DISABLE_MAIN_EVALUATION_RESULT_MERGE, ScopeInterface::SCOPE_STORE);
    }
}

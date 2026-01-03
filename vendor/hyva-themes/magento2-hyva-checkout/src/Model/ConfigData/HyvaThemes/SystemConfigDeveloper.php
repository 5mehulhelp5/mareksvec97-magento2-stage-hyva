<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\ConfigData\HyvaThemes;

use Hyva\Checkout\Model\ConfigData\HyvaThemes\Developer\SystemConfigAddressForms;
use Hyva\Checkout\Model\ConfigData\HyvaThemes\Developer\SystemConfigEvaluationApi;
use Hyva\Checkout\Model\ConfigData\HyvaThemes\Developer\SystemConfigExperimental;
use Hyva\Checkout\Model\ConfigData\HyvaThemes\Developer\SystemConfigFixesWorkarounds;
use Hyva\Checkout\Model\ConfigData\HyvaThemes\Developer\SystemConfigPlaceOrder;
use Hyva\Checkout\Model\ConfigData\HyvaThemes\Developer\SystemConfigShippingBillingForm;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\ScopeInterface;

class SystemConfigDeveloper
{
    public const XML_PATH_MOBILE_USERAGENT_REGEX = 'hyva_themes_checkout/developer/mobile_useragent_regex';

    protected ScopeConfigInterface $scopeConfig;

    private SystemConfigFixesWorkarounds $systemConfigFixesWorkarounds;
    private SystemConfigPlaceOrder $systemConfigPlaceOrder;
    private SystemConfigEvaluationApi $systemConfigEvaluationApi;
    private SystemConfigAddressForms $systemConfigAddressForms;
    private SystemConfigShippingBillingForm $systemConfigShippingBillingForm;
    private SystemConfigExperimental $systemConfigExperimental;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ?SystemConfigFixesWorkarounds $systemConfigFixesWorkarounds = null,
        ?SystemConfigPlaceOrder $systemConfigPlaceOrder = null,
        ?SystemConfigEvaluationApi $systemConfigEvaluationApi = null,
        ?SystemConfigAddressForms $systemConfigAddressForms = null,
        ?SystemConfigShippingBillingForm $systemConfigShippingBillingForm = null,
        ?SystemConfigExperimental $systemConfigExperimental = null
    ) {
        $this->scopeConfig = $scopeConfig;

        $this->systemConfigFixesWorkarounds = $systemConfigFixesWorkarounds
            ?: ObjectManager::getInstance()->get(SystemConfigFixesWorkarounds::class);
        $this->systemConfigPlaceOrder = $systemConfigPlaceOrder
            ?: ObjectManager::getInstance()->get(SystemConfigPlaceOrder::class);
        $this->systemConfigEvaluationApi = $systemConfigEvaluationApi
            ?: ObjectManager::getInstance()->get(SystemConfigEvaluationApi::class);
        $this->systemConfigAddressForms = $systemConfigAddressForms
            ?: ObjectManager::getInstance()->get(SystemConfigAddressForms::class);
        $this->systemConfigShippingBillingForm = $systemConfigShippingBillingForm
            ?: ObjectManager::getInstance()->get(SystemConfigShippingBillingForm::class);
        $this->systemConfigExperimental = $systemConfigExperimental
            ?: ObjectManager::getInstance()->get(SystemConfigExperimental::class);
    }

    public function evaluationApi(): SystemConfigEvaluationApi
    {
        return $this->systemConfigEvaluationApi;
    }

    public function addressForm(): SystemConfigAddressForms
    {
        return $this->systemConfigAddressForms;
    }

    public function shippingBillingForm(): SystemConfigShippingBillingForm
    {
        return $this->systemConfigShippingBillingForm;
    }

    public function placeOrder(): SystemConfigPlaceOrder
    {
        return $this->systemConfigPlaceOrder;
    }

    public function fixesWorkarounds(): SystemConfigFixesWorkarounds
    {
        return $this->systemConfigFixesWorkarounds;
    }

    public function experimental(): SystemConfigExperimental
    {
        return $this->systemConfigExperimental;
    }

    public function getMobileUserAgentRegex(): string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_MOBILE_USERAGENT_REGEX, ScopeInterface::SCOPE_STORE);
    }
}

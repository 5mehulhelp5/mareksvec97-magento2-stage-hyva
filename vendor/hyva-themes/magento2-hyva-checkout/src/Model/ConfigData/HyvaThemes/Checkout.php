<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\ConfigData\HyvaThemes;

use Hyva\Checkout\Model\Config\Source\BreadcrumbRenderStyles;
use Hyva\Checkout\Model\Config\Source\TooltipRenderStyles;
use Magento\Customer\Model\Session as SessionCustomer;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\ScopeInterface;

class Checkout
{
    public const XML_PATH_HTC_BREADCRUMBS_SWP = 'hyva_themes_checkout/breadcrumbs/show_waypoints';
    public const XML_PATH_HTC_BREADCRUMBS_STYLE = 'hyva_themes_checkout/breadcrumbs/display_type';
    public const XML_PATH_HTC_BREADCRUMBS_SC = 'hyva_themes_checkout/breadcrumbs/show_cart_item';
    public const XML_PATH_HTC_NAVIGATION_SBB = 'hyva_themes_checkout/navigation/show_back_button';
    public const XML_PATH_HTC_NAVIGATION_SBTCB = 'hyva_themes_checkout/navigation/show_back_to_cart_button';
    public const XML_PATH_HTC_SUMMARY_CI_UNFOLD = 'hyva_themes_checkout/order_summary/cart_items/unfold';
    public const XML_PATH_HTC_SUMMARY_CI_LIMIT = 'hyva_themes_checkout/order_summary/cart_items/limit';

    public const XML_PATH_CO_AGREEMENT_TYPE = 'checkout/options/agreements_type';
    public const XML_PATH_CO_AGREEMENT_MESSAGE = 'checkout/options/agreements_message';
    public const XML_PATH_CO_AGREEMENT_PAGE = 'checkout/options/agreements_page';

    protected ScopeConfigInterface $scopeConfig;
    protected SessionCustomer $sessionCustomer;
    protected Json $serializer;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        SessionCustomer $sessionCustomer,
        Json $serializer
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->sessionCustomer = $sessionCustomer;
        $this->serializer = $serializer;
    }

    public function showNavigationBackButton(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_HTC_NAVIGATION_SBB, ScopeInterface::SCOPE_STORE);
    }

    public function showNavigationBackToCartButton(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_HTC_NAVIGATION_SBTCB, ScopeInterface::SCOPE_STORE);
    }

    public function showBreadcrumbsWaypoints(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_HTC_BREADCRUMBS_SWP, ScopeInterface::SCOPE_STORE);
    }
    public function getBreadcrumbsStyle(): string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_HTC_BREADCRUMBS_STYLE, ScopeInterface::SCOPE_STORE) ?? BreadcrumbRenderStyles::CLASSIC;
    }

    public function showBreadcrumbAsClassic(): bool
    {
        return $this->showBreadcrumbAs(BreadcrumbRenderStyles::CLASSIC);
    }

    public function showBreadcrumbAsProgressBar(): bool
    {
        return $this->showBreadcrumbAs(BreadcrumbRenderStyles::PROGRESS_BAR);
    }

    public function showBreadcrumbsCart(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_HTC_BREADCRUMBS_SC, ScopeInterface::SCOPE_STORE);
    }

    public function getShippingEavAttributeFormFieldsMapping(string $by = 'attribute_code'): array
    {
        $mapping = $this->getComponentValue('shipping_address/eav_attribute_form_fields');
        $mapping = $this->serializer->unserialize($mapping);

        return array_column(array_map(function ($k, array $v) use ($by) {
            return array_key_exists($by, $v) ? [$v[$by], $v] : [$k, $v];
        }, array_keys($mapping), $mapping), 1, 0);
    }

    public function getBillingEavAttributeFormFieldsMapping(string $by = 'attribute_code'): array
    {
        $mapping = $this->getComponentValue('billing_address/eav_attribute_form_fields');
        $mapping = $this->serializer->unserialize($mapping);

        return array_column(array_map(function ($k, array $v) use ($by) {
            return array_key_exists($by, $v) ? [$v[$by], $v] : [$k, $v];
        }, array_keys($mapping), $mapping), 1, 0);
    }

    public function canCartItemsUnfold(?int $currentCartItemsAmount = null): bool
    {
        $canUnfold = $this->scopeConfig->isSetFlag(self::XML_PATH_HTC_SUMMARY_CI_UNFOLD, ScopeInterface::SCOPE_STORE);

        if ($canUnfold === true
            && $currentCartItemsAmount
            && $currentCartItemsAmount < $this->getCartItemsUnfoldLimit()) {
            return true;
        }

        return false;
    }

    public function getCartItemsUnfoldLimit(): int
    {
        return (int) $this->scopeConfig->getValue(self::XML_PATH_HTC_SUMMARY_CI_LIMIT, ScopeInterface::SCOPE_STORE);
    }

    public function getTermsAndConditionsType(): string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_CO_AGREEMENT_TYPE, ScopeInterface::SCOPE_STORE);
    }

    public function getTermsAndConditionsMessage(): string
    {
        /* Starting from version 1.1.5, this configuration can not be empty because of a adminhtml system xml validation,
           unless it's the initial installment with no explicitly set value from within config.xml. */
        return $this->scopeConfig->getValue(self::XML_PATH_CO_AGREEMENT_MESSAGE, ScopeInterface::SCOPE_STORE) ?? '';
    }

    public function getTermsAndConditionsPage(): string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_CO_AGREEMENT_PAGE, ScopeInterface::SCOPE_STORE);
    }

    public function getComponentValue(
        string $path,
        string $scopeType = ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ) {
        return $this->getGroupValue($path, 'component', $scopeType, $scopeCode);
    }

    /**
     * Retrieve grouped config value by path and scope.
     *
     * @param string $path The path through the tree of configuration values, e.g., 'store_information/name'
     * @param string $group
     * @param string $scopeType The scope to use to determine config value, e.g., 'store' or 'default'
     * @param null|int|string $scopeCode
     * @return mixed
     */
    public function getGroupValue(
        string $path,
        string $group = 'general',
        string $scopeType = ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ) {
        $path = sprintf('hyva_themes_checkout/%s/%s', $group, trim($path));
        return $this->scopeConfig->getValue($path, $scopeType, $scopeCode);
    }

    /**
     * Retrieve component config flag by path and scope.
     *
     * @param string $path The path through the tree of configuration values, e.g., 'general/store_information/name'
     * @param string $scopeType The scope to use to determine config value, e.g., 'store' or 'default'
     * @param null|int|string $scopeCode
     * @param string $group
     * @return bool
     */
    public function isSetFlagForComponent(
        string $path,
        string $scopeType = ScopeInterface::SCOPE_STORE,
        $scopeCode = null,
        string $group = 'component'
    ): bool {
        $path = sprintf('hyva_themes_checkout/%s/%s', $group, trim($path));
        return $this->scopeConfig->isSetFlag($path, $scopeType, $scopeCode);
    }
}

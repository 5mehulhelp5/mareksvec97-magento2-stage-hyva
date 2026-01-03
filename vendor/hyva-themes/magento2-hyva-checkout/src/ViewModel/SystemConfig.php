<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\ViewModel;

use Hyva\Checkout\Model\ConfigData\HyvaThemes\SystemConfigAddressForm;
use Hyva\Checkout\Model\ConfigData\HyvaThemes\SystemConfigBreadcrumbs;
use Hyva\Checkout\Model\ConfigData\HyvaThemes\SystemConfigComponents;
use Hyva\Checkout\Model\ConfigData\HyvaThemes\SystemConfigDesign;
use Hyva\Checkout\Model\ConfigData\HyvaThemes\SystemConfigDeveloper;
use Hyva\Checkout\Model\ConfigData\HyvaThemes\SystemConfigGeneral;
use Hyva\Checkout\Model\ConfigData\HyvaThemes\SystemConfigNavigation;
use Hyva\Checkout\Model\ConfigData\HyvaThemes\SystemConfigOrderSummary;
use Hyva\Checkout\Model\ConfigData\HyvaThemes\SystemConfigSigninRegistration;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Main entry point for receiving Hyvä Checkout system configuration settings.
 */
class SystemConfig implements ArgumentInterface
{
    private SystemConfigGeneral $systemConfigGeneral;
    private SystemConfigComponents $systemConfigComponents;
    private SystemConfigBreadcrumbs $systemConfigBreadcrumbs;
    private SystemConfigNavigation $systemConfigNavigation;
    private SystemConfigSigninRegistration $systemConfigSignInRegistration;
    private SystemConfigOrderSummary $systemConfigOrderSummary;
    private SystemConfigAddressForm $systemConfigAddressForm;
    private SystemConfigDesign $systemConfigDesign;
    private SystemConfigDeveloper $systemConfigDeveloper;

    public function __construct(
        SystemConfigGeneral $systemConfigGeneral,
        SystemConfigComponents $systemConfigComponents,
        SystemConfigBreadcrumbs $systemConfigBreadcrumbs,
        SystemConfigNavigation $systemConfigNavigation,
        SystemConfigSigninRegistration $systemConfigSignInRegistration,
        SystemConfigOrderSummary $systemConfigOrderSummary,
        SystemConfigAddressForm $systemConfigAddressForm,
        SystemConfigDesign $systemConfigDesign,
        SystemConfigDeveloper $systemConfigDeveloper
    ) {
        $this->systemConfigGeneral = $systemConfigGeneral;
        $this->systemConfigComponents = $systemConfigComponents;
        $this->systemConfigBreadcrumbs = $systemConfigBreadcrumbs;
        $this->systemConfigNavigation = $systemConfigNavigation;
        $this->systemConfigSignInRegistration = $systemConfigSignInRegistration;
        $this->systemConfigOrderSummary = $systemConfigOrderSummary;
        $this->systemConfigAddressForm = $systemConfigAddressForm;
        $this->systemConfigDesign = $systemConfigDesign;
        $this->systemConfigDeveloper = $systemConfigDeveloper;
    }

    public function general(): SystemConfigGeneral
    {
        return $this->systemConfigGeneral;
    }

    public function components(): SystemConfigComponents
    {
        return $this->systemConfigComponents;
    }

    public function breadcrumbs(): SystemConfigBreadcrumbs
    {
        return $this->systemConfigBreadcrumbs;
    }

    public function navigation(): SystemConfigNavigation
    {
        return $this->systemConfigNavigation;
    }

    public function signInRegistration(): SystemConfigSigninRegistration
    {
        return $this->systemConfigSignInRegistration;
    }

    public function orderSummary(): SystemConfigOrderSummary
    {
        return $this->systemConfigOrderSummary;
    }

    public function addressForm(): SystemConfigAddressForm
    {
        return $this->systemConfigAddressForm;
    }

    public function design(): SystemConfigDesign
    {
        return $this->systemConfigDesign;
    }

    public function developer(): SystemConfigDeveloper
    {
        return $this->systemConfigDeveloper;
    }
}

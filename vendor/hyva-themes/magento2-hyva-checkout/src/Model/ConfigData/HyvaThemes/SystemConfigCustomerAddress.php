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

class SystemConfigCustomerAddress
{
    public const XML_PATH_BILLING_AS_SHIPPING = 'hyva_themes_checkout/customer/address/billing_as_shipping';

    protected ScopeConfigInterface $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Can apply the customer shipping- as billing address by default.
     *
     * @return bool
     */
    public function canApplyBillingAsShippingAddress(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_BILLING_AS_SHIPPING, ScopeInterface::SCOPE_STORE);
    }
}

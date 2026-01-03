<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\ConfigData\HyvaThemes\AddressForm;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class SystemConfigStreet
{
    protected ScopeConfigInterface $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function hasLabelFor(int $line): bool
    {
        return ! empty($this->getLabelFor($line));
    }

    public function getLabelFor(int $line): string
    {
        return $this->scopeConfig->getValue(sprintf('hyva_themes_checkout/address_form/street/field_label_%s', $line), ScopeInterface::SCOPE_STORE) ?? '';
    }
}

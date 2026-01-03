<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form\EntityField\EavEntityAddress;

use Hyva\Checkout\Model\Form\EntityField\EavAttributeField;
use Magento\Config\Model\Config\Source\Nooptreq;
use Magento\Store\Model\ScopeInterface;

class GenderAttributeField extends EavAttributeField
{
    public function canRender(): bool
    {
        $showPrefix = $this->scopeConfig->getValue('customer/address/gender_show', ScopeInterface::SCOPE_STORE);
        return ! empty($showPrefix);
    }

    public function isRequired(): bool
    {
        $showPrefix = $this->scopeConfig->getValue('customer/address/gender_show', ScopeInterface::SCOPE_STORE);
        return ! (empty($showPrefix) || $showPrefix !== Nooptreq::VALUE_REQUIRED);
    }

    public function isOptional(): bool
    {
        $showPrefix = $this->scopeConfig->getValue('customer/address/gender_show', ScopeInterface::SCOPE_STORE);
        return ! (empty($showPrefix) || $showPrefix === Nooptreq::VALUE_OPTIONAL);
    }

    public function getOptions(): array
    {
        return $this->getAttribute()->getOptions();
    }
}

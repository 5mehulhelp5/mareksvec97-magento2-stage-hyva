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

class PrefixAttributeField extends EavAttributeField
{
    public function canRender(): bool
    {
        $showPrefix = $this->scopeConfig->getValue('customer/address/prefix_show', ScopeInterface::SCOPE_STORE);
        return ! empty($showPrefix);
    }

    public function isRequired(): bool
    {
        $showPrefix = $this->scopeConfig->getValue('customer/address/prefix_show', ScopeInterface::SCOPE_STORE);
        return ! (empty($showPrefix) || $showPrefix !== Nooptreq::VALUE_REQUIRED);
    }

    public function getOptions(): array
    {
        $result = $this->getData('options');

        if (is_array($result)) {
            return $result;
        }

        $prefixOptions = $this->scopeConfig->getValue('customer/address/prefix_options', ScopeInterface::SCOPE_STORE);

        if ($prefixOptions === null || empty(trim($prefixOptions))) {
            $this->setOptions([]);

            return [];
        }

        $result  = [];
        $options = explode(';', trim($prefixOptions));

        foreach ($options as $value) {
            $result[$value] = trim($value) ?: ' ';
        }

        if (! $this->isRequired() && trim(current($options))) {
            $result = array_merge([null => ' '], $result);
        }

        $this->setOptions($result);
        return $this->getData('options');
    }
}

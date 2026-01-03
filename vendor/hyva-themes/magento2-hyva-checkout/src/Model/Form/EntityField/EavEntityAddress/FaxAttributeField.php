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

class FaxAttributeField extends EavAttributeField
{
    public function canRender(): bool
    {
        $showFax = $this->scopeConfig->getValue('customer/address/fax_show', ScopeInterface::SCOPE_STORE);
        return ! empty($showFax);
    }

    public function isRequired(): bool
    {
        $showFax = $this->scopeConfig->getValue('customer/address/fax_show', ScopeInterface::SCOPE_STORE);
        return ! (empty($showFax) || $showFax !== Nooptreq::VALUE_REQUIRED);
    }
}

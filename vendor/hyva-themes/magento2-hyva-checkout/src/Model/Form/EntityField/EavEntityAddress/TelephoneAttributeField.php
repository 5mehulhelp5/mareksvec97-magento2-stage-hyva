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

class TelephoneAttributeField extends EavAttributeField
{
    /**
     * @deprecated use canRender instead
     * @see self::canRender()
     */
    public function isEnabled(): bool
    {
        return $this->canRender();
    }

    /**
     * @deprecated use isRequired instead
     * @see self::isRequired()
     */
    public function getRequired(): bool
    {
        return $this->isRequired();
    }

    public function canRender(): bool
    {
        $showTelephone = $this->scopeConfig->getValue('customer/address/telephone_show', ScopeInterface::SCOPE_STORE);
        return ! empty($showTelephone);
    }

    public function isRequired(): bool
    {
        $showTelephone = $this->scopeConfig->getValue('customer/address/telephone_show', ScopeInterface::SCOPE_STORE);
        return ! (empty($showTelephone) || $showTelephone !== Nooptreq::VALUE_REQUIRED);
    }

    public function getFrontendInput(): string
    {
        $input = parent::getFrontendInput();
        return $input === 'text' ? 'tel' : $input;
    }
}

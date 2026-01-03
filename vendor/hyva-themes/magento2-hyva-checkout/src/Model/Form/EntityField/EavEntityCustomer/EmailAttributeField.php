<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form\EntityField\EavEntityCustomer;

use Hyva\Checkout\Model\Form\EntityField\EavAttributeField;
use Magento\Quote\Api\Data\AddressInterface;

/**
 * @deprecated use a regular input field element instead.
 * @see \Hyva\Checkout\Model\Form\EntityField\Input
 */
class EmailAttributeField extends EavAttributeField
{
    public function getId(): string
    {
        return AddressInterface::KEY_EMAIL;
    }

    public function getFrontendInput(): string
    {
        return 'email';
    }

    public function getName(): string
    {
        return AddressInterface::KEY_EMAIL;
    }

    public function getLabel(): string
    {
        return 'Email address';
    }

    public function isRequired(): bool
    {
        return true;
    }

    public function getAutocomplete(): string
    {
        return 'email';
    }

    public function getDefaultValue()
    {
        return null;
    }

    public function isAutoSave(): bool
    {
        return true;
    }
}

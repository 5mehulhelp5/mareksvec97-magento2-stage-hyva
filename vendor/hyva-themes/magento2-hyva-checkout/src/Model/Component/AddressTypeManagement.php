<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Component;

class AddressTypeManagement
{
    protected AddressTypeInterface $addressTypeShipping;
    protected AddressTypeInterface $addressTypeBilling;

    public function __construct(
        AddressTypeInterface $addressTypeShipping,
        AddressTypeInterface $addressTypeBilling
    ) {
        $this->addressTypeShipping = $addressTypeShipping;
        $this->addressTypeBilling = $addressTypeBilling;
    }

    public function getAddressTypeShipping(): AddressTypeInterface
    {
        return $this->addressTypeShipping;
    }

    public function getAddressTypeBilling(): AddressTypeInterface
    {
        return $this->addressTypeBilling;
    }
}

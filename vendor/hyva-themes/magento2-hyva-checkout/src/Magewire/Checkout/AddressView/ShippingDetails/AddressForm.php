<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Magewire\Checkout\AddressView\ShippingDetails;

use Hyva\Checkout\Magewire\Checkout\AddressView\AbstractMagewireAddressForm;
use Hyva\Checkout\Model\Component\AddressTypeInterface;
use Hyva\Checkout\Model\Form\EntityFormInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class AddressForm extends AbstractMagewireAddressForm
{
    public array $address = [
        'id' => null
    ];

    public function canAutoSave(): bool
    {
        return ! $this->modal['shown'];
    }

    public function canSaveToAddressBook(): bool
    {
        try {
            return ! $this->sessionCheckout->getQuote()->getCustomerIsGuest() && ! (bool) ($this->address['id'] ?? false);
        } catch (NoSuchEntityException | LocalizedException $exception) {
            return false;
        }
    }

    public function getAutoSaveTimeout(): int
    {
        return $this->autoSaveTimeout;
    }

    public function canCancel(): bool
    {
        return true;
    }

    public function getAddressType(): AddressTypeInterface
    {
        return $this->addressTypeManagement->getAddressTypeShipping();
    }
}

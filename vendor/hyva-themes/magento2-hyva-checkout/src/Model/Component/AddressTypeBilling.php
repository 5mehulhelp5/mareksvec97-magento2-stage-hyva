<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Component;

use Hyva\Checkout\Model\Form\EntityFormInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote\Address;

class AddressTypeBilling extends AbstractAddressType
{
    public const TYPE = 'billing';

    public function getNamespace(): string
    {
        return self::TYPE;
    }

    /**
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getQuoteAddress(): Address
    {
        return $this->sessionCheckout->getQuote()->getBillingAddress();
    }

    public function getForm(): EntityFormInterface
    {
        return $this->entityFormProvider->getBillingAddressForm();
    }

    public function getComponentViewBlock()
    {
        try {
            $quote = $this->sessionCheckout->getQuote();
            $addressShipping = $quote->getShippingAddress();

            if ($quote->isVirtual()) {
                if ($quote->getCustomerIsGuest() || count($quote->getCustomer()->getAddresses()) === 0) {
                    return $this->layout->getBlock(sprintf(AddressTypeInterface::VIEW_ADDRESS_FORM, $this));
                }

                return $this->layout->getBlock(sprintf(AddressTypeInterface::VIEW_ADDRESS_LIST, $this));
            }
        } catch (NoSuchEntityException | LocalizedException $exception) {
            return false;
        }

        if ($addressShipping->getSameAsBilling()) {
            return $this->layout->getBlock(sprintf(AddressTypeInterface::VIEW_ADDRESS, $this));
        }

        return false;
    }
}

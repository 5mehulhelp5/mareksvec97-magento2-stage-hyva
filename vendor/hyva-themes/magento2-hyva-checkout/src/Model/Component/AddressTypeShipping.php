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

class AddressTypeShipping extends AbstractAddressType
{
    public const TYPE = 'shipping';

    /**
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getQuoteAddress(): Address
    {
        return $this->sessionCheckout->getQuote()->getShippingAddress();
    }

    public function getForm(): EntityFormInterface
    {
        return $this->entityFormProvider->getShippingAddressForm();
    }

    public function getComponentViewBlock()
    {
        return false;
    }

    public function getNamespace(): string
    {
        return self::TYPE;
    }
}

<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\ViewModel\Checkout;

use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class ShippingSummary implements ArgumentInterface
{
    protected SessionCheckout $sessionCheckout;

    /**
     * @param SessionCheckout $sessionCheckout
     */
    public function __construct(
        SessionCheckout $sessionCheckout
    ) {
        $this->sessionCheckout = $sessionCheckout;
    }

    /**
     * @return AddressInterface|null
     */
    public function getShippingAddress(): ?AddressInterface
    {
        try {
            return $this->sessionCheckout->getQuote()->getShippingAddress()->exportCustomerAddress();
        } catch (LocalizedException | NoSuchEntityException $exception) {
            return null;
        }
    }

    /**
     * @return string|null
     */
    public function getShippingCarrier(): ?string
    {
        try {
            return $this->sessionCheckout->getQuote()->getShippingAddress()->getShippingDescription();
        } catch (LocalizedException | NoSuchEntityException $exception) {
            return null;
        }
    }
}

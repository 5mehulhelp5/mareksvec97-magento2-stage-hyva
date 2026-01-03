<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */
declare(strict_types=1);

namespace Hyva\CheckoutPayPal\Model\Payment;

use Magento\Checkout\Model\Session as SessionCheckout;

class PaypalExpressRedirect
{
    private SessionCheckout $sessionCheckout;

    public function __construct(SessionCheckout $sessionCheckout)
    {
        $this->sessionCheckout = $sessionCheckout;
    }

    public function getRedirectStep(): string
    {
        $quote = $this->sessionCheckout->getQuote();

        if ($quote->isVirtual()) {
            return 'payment';
        }

        $shippingAddress = $quote->getShippingAddress();
        $shippingMethod = $shippingAddress->getShippingMethod();

        return !$shippingMethod || $shippingAddress->validate() !== true ? 'shipping' : 'payment';
    }
}

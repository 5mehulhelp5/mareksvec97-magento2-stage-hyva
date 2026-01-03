<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */
declare(strict_types=1);

namespace Hyva\CheckoutPayPal\Model\Magewire\Payment;

use Hyva\Checkout\Model\Magewire\Payment\AbstractPlaceOrderService;
use Hyva\CheckoutPayPal\Model\Payment\PaypalPaymentInformation;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Model\Quote;

class PaypalPlaceOrderService extends AbstractPlaceOrderService
{
    private PaypalPaymentInformation $paypalPaymentInformation;

    public function __construct(
        CartManagementInterface $cartManagement,
        PaypalPaymentInformation $paypalPaymentInformation
    ) {
        parent::__construct($cartManagement);
        $this->paypalPaymentInformation = $paypalPaymentInformation;
    }

    public function placeOrder(Quote $quote): int
    {
        $this->paypalPaymentInformation->prepareAdditionalInformation($quote);

        return parent::placeOrder($quote);
    }
}

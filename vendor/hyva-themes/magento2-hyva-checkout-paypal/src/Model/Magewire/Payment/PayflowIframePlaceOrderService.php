<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\CheckoutPayPal\Model\Magewire\Payment;

use Hyva\CheckoutPayPal\Model\Payment\PayflowIframeProvider;
use Hyva\CheckoutPayPal\Model\Payment\PaypalPaymentInformation;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Model\Quote;

class PayflowIframePlaceOrderService extends PaypalPlaceOrderService
{
    private PayflowIframeProvider $payflowIframeProvider;

    public function __construct(
        CartManagementInterface $cartManagement,
        PaypalPaymentInformation $paypalPaymentInformation,
        PayflowIframeProvider $payflowIframeProvider
    ) {
        parent::__construct($cartManagement, $paypalPaymentInformation);
        $this->payflowIframeProvider = $payflowIframeProvider;
    }

    public function getRedirectUrl(Quote $quote, ?int $orderId = null): string
    {
        $paymentCode = $quote->getPayment()
            ->getMethod();

        if (in_array($paymentCode, $this->payflowIframeProvider->getAllowedMethods(), true)) {
            return 'hyva_paypal/payflow/iframe';
        }

        return parent::getRedirectUrl($quote, $orderId);
    }
}

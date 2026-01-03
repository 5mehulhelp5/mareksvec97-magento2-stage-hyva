<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */
declare(strict_types=1);

namespace Hyva\CheckoutPayPal\ViewModel\Payment\Method;

use Hyva\CheckoutPayPal\Model\Payment\PayflowIframeProvider;
use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class PayflowIframe implements ArgumentInterface
{
    private SessionCheckout $sessionCheckout;

    private PayflowIframeProvider $payflowIframeProvider;

    private ?string $orderPaymentCode = null;

    public function __construct(
        SessionCheckout $sessionCheckout,
        PayflowIframeProvider $payflowIframeProvider
    ) {
        $this->sessionCheckout = $sessionCheckout;
        $this->payflowIframeProvider = $payflowIframeProvider;
    }

    public function getMethodCode(): string
    {
        if ($this->orderPaymentCode === null) {
            $order = $this->sessionCheckout->getLastRealOrder();

            if (!$this->payflowIframeProvider->isIframeAllowed($order)) {
                throw new LocalizedException(__('This payment is not allowed to pay with credit card'));
            }

            $this->orderPaymentCode = $order->getPayment()
                ->getMethod();
        }

        return $this->orderPaymentCode;
    }

    public function getIframeUrl(): string
    {
        return $this->payflowIframeProvider->getIframeUrl($this->getMethodCode());
    }
}

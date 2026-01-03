<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */
declare(strict_types=1);

namespace Hyva\CheckoutPayPal\ViewModel\Payment;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Paypal\Model\PayLaterConfig;
use Magento\Paypal\Model\SdkUrl;

class PayLater implements ArgumentInterface
{
    private SdkUrl $sdkUrl;

    private PayLaterConfig $payLaterConfig;

    public function __construct(
        SdkUrl $sdkUrl,
        PayLaterConfig $payLaterConfig
    ) {
        $this->sdkUrl = $sdkUrl;
        $this->payLaterConfig = $payLaterConfig;
    }

    public function isPayLaterEnabled(): bool
    {
        return $this->payLaterConfig->isEnabled(PayLaterConfig::CHECKOUT_PAYMENT_PLACEMENT);
    }

    public function getSdkUrl(): string
    {
        return $this->sdkUrl->getUrl();
    }
}

<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */
declare(strict_types=1);

namespace Hyva\Checkout\Model\Payment\Checks;

use Hyva\Checkout\Model\Config as HyvaCheckoutConfig;
use Hyva\Checkout\Model\ConfigData\HyvaThemes\SystemConfigPayment;
use Magento\Payment\Model\Checks\ZeroTotal as MagentoZeroTotal;
use Magento\Payment\Model\MethodInterface;
use Magento\Quote\Model\Quote;

class ZeroTotal extends MagentoZeroTotal
{
    private HyvaCheckoutConfig $hyvaCheckoutConfig;
    private SystemConfigPayment $hyvaCheckoutPaymentConfig;

    public function __construct(
        HyvaCheckoutConfig $hyvaCheckoutConfig,
        SystemConfigPayment $hyvaCheckoutPaymentConfig
    ) {
        $this->hyvaCheckoutConfig = $hyvaCheckoutConfig;
        $this->hyvaCheckoutPaymentConfig = $hyvaCheckoutPaymentConfig;
    }

    /**
     * Remove Magento's default limitation of only permitting the 'free' payment method being used for zero total
     * orders when using a Hyvä checkout - instead permitting any of the selected zero total payment methods.
     *
     * @param MethodInterface $paymentMethod
     * @param Quote $quote
     * @return bool
     */
    public function isApplicable(MethodInterface $paymentMethod, Quote $quote): bool
    {
        if ($quote->getBaseGrandTotal() < 0.0001 // mirror default validator condition
            && $this->hyvaCheckoutConfig->isHyvaCheckout($this->hyvaCheckoutConfig->getActiveCheckoutNamespace())
            && $this->hyvaCheckoutPaymentConfig->removeNonZeroPaymentMethods()
        ) {
            return in_array(
                $paymentMethod->getCode(),
                $this->hyvaCheckoutPaymentConfig->getEnabledZeroPaymentMethods()
            );
        }

        return parent::isApplicable($paymentMethod, $quote);
    }
}

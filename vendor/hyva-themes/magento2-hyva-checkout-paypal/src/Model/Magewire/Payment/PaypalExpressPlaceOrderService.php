<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */
declare(strict_types=1);

namespace Hyva\CheckoutPayPal\Model\Magewire\Payment;

use Hyva\CheckoutPayPal\Model\Payment\BillingAgreement;
use Hyva\CheckoutPayPal\Model\Payment\PaypalExpressAuthorization;
use Hyva\CheckoutPayPal\Model\Payment\PaypalExpressConfig;
use Hyva\CheckoutPayPal\Model\Payment\PaypalPaymentInformation;
use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Paypal\Model\Express\Checkout as PaypalExpressCheckout;
use Magento\Paypal\Model\Config as PaypalConfig;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Model\Quote;

class PaypalExpressPlaceOrderService extends PaypalPlaceOrderService
{
    private PaypalExpressAuthorization $paypalExpressAuthorization;

    private SessionCheckout $sessionCheckout;

    private PaypalExpressConfig $paypalExpressConfig;

    private BillingAgreement $billingAgreement;

    private string $redirectPath;

    public function __construct(
        CartManagementInterface $cartManagement,
        PaypalPaymentInformation $paypalPaymentInformation,
        SessionCheckout $sessionCheckout,
        PaypalExpressConfig $paypalExpressConfig,
        PaypalExpressAuthorization $paypalExpressAuthorization,
        BillingAgreement  $billingAgreement,
        string $redirectPath = 'paypal/express/start'
    ) {
        parent::__construct($cartManagement, $paypalPaymentInformation);

        $this->sessionCheckout = $sessionCheckout;
        $this->paypalExpressConfig = $paypalExpressConfig;
        $this->paypalExpressAuthorization = $paypalExpressAuthorization;
        $this->billingAgreement = $billingAgreement;
        $this->redirectPath = $redirectPath;
    }

    public function canPlaceOrder(): bool
    {
        $quote = $this->sessionCheckout->getQuote();
        if ($this->paypalExpressAuthorization->isAuthorized($this->sessionCheckout->getQuote())) {
            $quotePayment = $quote->getPayment();

            if ($quotePayment->getMethod() === PaypalConfig::METHOD_WPP_PE_BML) {
                $quotePayment->setMethod(PaypalConfig::METHOD_WPP_PE_EXPRESS);
            }

            return true;
        }

        return false;
    }

    public function getRedirectUrl(Quote $quote, ?int $orderId = null): string
    {
        if ($this->paypalExpressAuthorization->isAuthorized($quote)) {
            return parent::getRedirectUrl($quote, $orderId);
        }

        $quotePayment = $quote->getPayment();

        $urlPathPrefix = '';
        if ($this->billingAgreement->shouldCreateAgreement($quote) &&
            !$this->paypalExpressConfig->isInContext($quotePayment->getMethod())
        ) {
            $urlPathPrefix .= '/' . PaypalExpressCheckout::PAYMENT_INFO_TRANSPORT_BILLING_AGREEMENT . '/1';
        }

        return $this->redirectPath . $urlPathPrefix;
    }
}

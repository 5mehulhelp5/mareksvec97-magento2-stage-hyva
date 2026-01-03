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
use Magento\Paypal\Model\Config as PaypalConfig;
use Magento\Paypal\Model\Express\Checkout as PaypalCheckout;
use Magento\Paypal\Model\Express\Checkout\Factory as PaypalCheckoutFactory;
use Magento\Framework\Session\Generic as SessionPaypal;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;

class PaypalExpressConfig
{
    private CartRepositoryInterface $cartRepository;

    private PaypalConfig $paypalConfig;

    private PaypalCheckoutFactory $paypalCheckoutFactory;

    private SessionCheckout $sessionCheckout;

    private SessionPaypal $sessionPaypal;

    private ?PaypalCheckout $checkoutModel = null;

    public function __construct(
        CartRepositoryInterface $cartRepository,
        PaypalConfig $paypalConfig,
        PaypalCheckoutFactory $paypalCheckoutFactory,
        SessionCheckout $sessionCheckout,
        SessionPaypal $sessionPaypal
    ) {
        $this->cartRepository = $cartRepository;
        $this->paypalConfig = $paypalConfig;
        $this->paypalCheckoutFactory = $paypalCheckoutFactory;
        $this->sessionCheckout = $sessionCheckout;
        $this->sessionPaypal = $sessionPaypal;
    }

    public function isInContext(string $methodCode): bool
    {
        $this->paypalConfig->setMethodCode($methodCode);
        return (bool) $this->paypalConfig->getValue('in_context');
    }

    public function getCheckoutModel(CartInterface $quote): PaypalCheckout
    {
        if ($this->checkoutModel === null) {
            $quotePayment = $quote->getPayment();

            $this->paypalConfig->setMethodCode($quotePayment->getMethod() ?: PaypalConfig::METHOD_WPP_EXPRESS);
            $parameters = ['params' => ['quote' => $quote, 'config' => $this->paypalConfig]];

            $this->checkoutModel = $this->paypalCheckoutFactory->create(PaypalCheckout::class, $parameters);
        }

        return $this->checkoutModel;
    }

    public function canSkipReview(CartInterface $quote): bool
    {
        return $this->getCheckoutModel($quote)->canSkipOrderReviewStep();
    }

    public function getPaypalQuote(): CartInterface
    {
        if ($quoteId = $this->sessionPaypal->getQuoteId()) {
            $quote = $this->cartRepository->get($quoteId);
            $this->sessionCheckout->replaceQuote($quote);
        } else {
            $quote = $this->sessionCheckout->getQuote();
        }

        return $quote;
    }
}

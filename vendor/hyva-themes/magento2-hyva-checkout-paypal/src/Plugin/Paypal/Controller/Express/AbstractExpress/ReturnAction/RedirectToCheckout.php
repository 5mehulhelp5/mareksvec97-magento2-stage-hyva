<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */
declare(strict_types=1);

namespace Hyva\CheckoutPayPal\Plugin\Paypal\Controller\Express\AbstractExpress\ReturnAction;

use Hyva\Checkout\Model\Config as HyvaCheckoutConfig;
use Hyva\CheckoutPayPal\Model\CheckoutType;
use Hyva\CheckoutPayPal\Model\Payment\PaypalExpressAuthorization;
use Hyva\CheckoutPayPal\Model\Payment\PaypalExpressConfig;
use Hyva\CheckoutPayPal\Model\Payment\PaypalExpressRedirect;
use Hyva\CheckoutPayPal\Model\Payment\PaypalPaymentInformation;
use Hyva\CheckoutPayPal\Plugin\Paypal\Controller\Express\AbstractExpress\AbstractPayPalExpressPlugin;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Paypal\Controller\Express\AbstractExpress\ReturnAction;

class RedirectToCheckout extends AbstractPayPalExpressPlugin
{
    private SessionCheckout $sessionCheckout;
    private RedirectFactory $redirectFactory;
    private CheckoutType $checkoutType;
    private PaypalExpressAuthorization $paypalExpressAuthorization;
    private PaypalExpressConfig $paypalExpressConfig;
    private PaypalExpressRedirect $paypalExpressRedirect;
    private PaypalPaymentInformation $paypalPaymentInformation;
    private bool $hasAuthorization = false;

    public function __construct(
        HyvaCheckoutConfig $hyvaCheckoutConfig,
        SessionCheckout $sessionCheckout,
        RedirectFactory $redirectFactory,
        CheckoutType $checkoutType,
        PaypalExpressAuthorization $paypalExpressAuthorization,
        PaypalExpressConfig $paypalExpressConfig,
        PaypalExpressRedirect $paypalExpressRedirect,
        PaypalPaymentInformation $paypalPaymentInformation
    ) {
        parent::__construct($hyvaCheckoutConfig);

        $this->sessionCheckout = $sessionCheckout;
        $this->redirectFactory = $redirectFactory;
        $this->checkoutType = $checkoutType;
        $this->paypalExpressAuthorization = $paypalExpressAuthorization;
        $this->paypalExpressConfig = $paypalExpressConfig;
        $this->paypalExpressRedirect = $paypalExpressRedirect;
        $this->paypalPaymentInformation = $paypalPaymentInformation;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeExecute(ReturnAction $returnAction): array
    {
        $quote = $this->sessionCheckout->getQuote();
        $this->hasAuthorization = $this->paypalExpressAuthorization->isAuthorized($quote);

        if (!$this->shouldRedirectToCheckout()) {
            $this->paypalPaymentInformation->prepareAdditionalInformation($quote);
        }

        return [];
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterExecute(ReturnAction $returnAction, ?Redirect $resultRedirect = null): ?Redirect
    {
        if ($this->checkoutType->isHyvaCheckout() && $this->shouldRedirectToCheckout()) {
            if (!$resultRedirect) {
                $resultRedirect = $this->redirectFactory->create();
            }

            $resultRedirect->setPath(
                'checkout',
                [
                    '_query' => [
                        'step' => $this->isMultiStepCheckout()
                            ? $this->paypalExpressRedirect->getRedirectStep()
                            : null,
                    ]
                ]
            );
        }

        return $resultRedirect;
    }

    private function shouldRedirectToCheckout(): bool
    {
        $quote = $this->sessionCheckout->getQuote();
        $quotePayment = $quote->getPayment();

        return !$this->paypalExpressConfig->canSkipReview($quote) ||
            (!$this->hasAuthorization && $this->paypalExpressConfig->isInContext($quotePayment->getMethod()));
    }
}

<?php
/*
 * Hyvä Themes - https://hyva.io
 *  Copyright © Hyvä Themes 2020-present. All rights reserved.
 *  This product is licensed per Magento install
 *  See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\CheckoutStripe\Magewire\Payment\Method;

use Hyva\CheckoutStripe\Service\Stripe\Quote\PaymentElementOptions;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magewirephp\Magewire\Component;
use StripeIntegration\Payments\Model\Config;
use StripeIntegration\Payments\Model\Ui\ConfigProvider;

class StripePayments extends Component
{
    private Config $stripeConfig;
    private Session $checkoutSession;
    private ConfigProvider $configProvider;

    private PaymentElementOptions $paymentElementOptions;

    private CartRepositoryInterface $cartRepository;
    private ?array $loadedConfig;
    public float $amount = 0.0;
    public string $currencyCode = '';
    public string $paymentMethodId = '';
    public ?string $clientSecret = null;
    public array $paymentElementData = [];
    public array $elementOptions = [];

    public function __construct(
        Session $checkoutSession,
        CartRepositoryInterface $cartRepository,
        Config $stripeConfig,
        ConfigProvider $configProvider,
        PaymentElementOptions $paymentElementOptions
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->cartRepository = $cartRepository;
        $this->stripeConfig = $stripeConfig;
        $this->configProvider = $configProvider;
        $this->paymentElementOptions = $paymentElementOptions;
    }

    public function boot(): void
    {
        $cart = $this->checkoutSession->getQuote();

        if (! $cart->getId()) {
            return;
        }

        $this->amount = round($cart->getGrandTotal() * 100);
        $this->currencyCode = strtolower($cart->getQuoteCurrencyCode());
    }

    public function getPublishableKey(): ?string
    {
        return $this->stripeConfig->getPublishableKey();
    }

    public function getAppInfo(): array
    {
        return $this->stripeConfig->getAppInfo(true);
    }

    public function loadElementOptions(string $method): void
    {
        $elementOptions = $this->getConfig($method)['elementOptions'];

        if ($elementOptions['mode'] != 'setup') {
            $elementOptions['amount'] = $this->amount;
            $elementOptions['currency'] = $this->currencyCode;
        }

        $this->elementOptions = $elementOptions;
    }

    public function getPaymentElementOptions(string $method): array
    {
        $this->paymentElementData = $this->paymentElementOptions->get($this->getConfig($method)['initParams']);
        if (empty($this->paymentElementData['wallets'])) {
            unset($this->paymentElementData['wallets']);
        }

        return $this->paymentElementData;
    }

    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function setPaymentMethodId(string $value, ?string $cvcToken = null): string
    {
        $quote = $this->checkoutSession->getQuote();
        $payment = $quote->getPayment();

        $payment->setAdditionalInformation('payment_method', $value);
        $payment->setAdditionalInformation('payment_element', true);
        $payment->setAdditionalInformation('manual_authentication', 'card');

        if ($cvcToken) {
            $payment->setAdditionalInformation('cvc_token', $cvcToken);
        }

        $this->cartRepository->save($quote);
        return $value;
    }

    private function getConfig(string $method): array
    {
        if (! isset($this->loadedConfig)) {
            $this->loadedConfig = $this->configProvider->getConfig()['payment'][$method];
        }

        return $this->loadedConfig;
    }
}

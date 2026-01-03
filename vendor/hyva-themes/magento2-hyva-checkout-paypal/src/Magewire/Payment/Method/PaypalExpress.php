<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */
declare(strict_types=1);

namespace Hyva\CheckoutPayPal\Magewire\Payment\Method;

use Hyva\Checkout\Model\Magewire\Component\EvaluationInterface;
use Hyva\Checkout\Model\Magewire\Component\EvaluationResultFactory;
use Hyva\Checkout\Model\Magewire\Component\EvaluationResultInterface;
use Hyva\CheckoutPayPal\Model\Payment\BillingAgreement;
use Hyva\CheckoutPayPal\Model\Payment\PaypalExpressAuthorization;
use Hyva\CheckoutPayPal\Model\Payment\PaypalExpressConfig;
use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Framework\Exception\LocalizedException;
use Magento\Paypal\Model\Config as PaypalConfig;
use Magento\Quote\Api\Data\CartInterface;
use Magewirephp\Magewire\Component;
use Magewirephp\Magewire\Model\Element\Redirect;

class PaypalExpress extends Component implements EvaluationInterface
{
    public bool $isAuthorized = false;

    public string $fundingSource = '';

    public bool $createBillingAgreement = false;

    /**
     * @var array
     */
    protected $listeners = [
        'paypal_express_authorized' => 'refresh'
    ];

    /**
     * @var array
     */
    protected $loader = [
        'authorize' => true,
        'cancel' => true,
        'edit' => true,
        'createBillingAgreement' => false
    ];

    private SessionCheckout $sessionCheckout;

    private PaypalConfig $paypalConfig;

    private PaypalExpressAuthorization $paypalExpressAuthorization;

    private PaypalExpressConfig $paypalExpressConfig;

    private BillingAgreement $billingAgreement;

    private string $methodCode;

    private string $cancelPath;

    public function __construct(
        SessionCheckout $sessionCheckout,
        PaypalConfig $paypalConfig,
        PaypalExpressAuthorization $paypalExpressAuthorization,
        PaypalExpressConfig $paypalExpressConfig,
        BillingAgreement $billingAgreement,
        string $methodCode = PaypalConfig::METHOD_WPP_EXPRESS,
        string $cancelPath = 'paypal/express/cancel'
    ) {
        $this->sessionCheckout = $sessionCheckout;
        $this->paypalConfig = $paypalConfig;
        $this->paypalExpressAuthorization = $paypalExpressAuthorization;
        $this->paypalExpressConfig = $paypalExpressConfig;
        $this->billingAgreement = $billingAgreement;
        $this->methodCode = $methodCode;
        $this->cancelPath = $cancelPath;
    }

    public function boot(): void
    {
        $quote = $this->getQuote();

        $this->isAuthorized = $this->paypalExpressAuthorization->isAuthorized($quote);
        $this->fundingSource = $this->paypalExpressAuthorization->getFundingSource($quote);

        $this->createBillingAgreement = $this->billingAgreement->shouldCreateAgreement($quote);
    }

    public function isInContext(): bool
    {
        return $this->paypalExpressConfig->isInContext($this->getMethodCode());
    }

    public function getMethodCode(): string
    {
        return $this->methodCode;
    }

    public function authorize(string $token, string $payer, string $funding): void
    {
        try {
            $quote = $this->sessionCheckout->getQuote();
            $this->paypalExpressAuthorization->authorize($quote, $token, $payer, $funding);

            $this->emit('paypal_express_authorized');
        } catch (LocalizedException $e) {
            $this->dispatchErrorMessage($e->getMessage());
        }
    }

    public function edit(): Redirect
    {
        $token = $this->paypalExpressAuthorization->getToken($this->getQuote());
        return $this->redirect($this->paypalConfig->getExpressCheckoutEditUrl($token));
    }

    public function cancel(): Redirect
    {
        $this->paypalExpressAuthorization->cancel($this->getQuote());
        return $this->redirect($this->cancelPath);
    }

    public function updatedCreateBillingAgreement(bool $createBillingAgreement): bool
    {
        $quote = $this->sessionCheckout->getQuote();
        $this->billingAgreement->setCreateAgreement($quote, $createBillingAgreement);

        return $createBillingAgreement;
    }

    public function evaluateCompletion(EvaluationResultFactory $resultFactory): EvaluationResultInterface
    {
        if (!$this->isAuthorized && $this->isInContext()) {
            return $resultFactory->createErrorMessage()
                ->withMessage((string) __('Please use paypal buttons to pay'));
        }

        return $resultFactory->createSuccess();
    }

    public function getQuote(): CartInterface
    {
        return $this->sessionCheckout->getQuote();
    }
}

<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */
declare(strict_types=1);

namespace Hyva\CheckoutPayPal\Magewire\Payment\Method;

use Exception;
use Hyva\CheckoutPayPal\Model\Command\ValidateReCaptchaCommand;
use Hyva\CheckoutPayPal\Model\Data\CardDetailsFactory;
use Hyva\Checkout\Model\Magewire\Component\EvaluationResultFactory;
use Hyva\Checkout\Model\Magewire\Component\EvaluationResultInterface;
use Hyva\Checkout\Model\Magewire\Component\EvaluationInterface;
use Hyva\CheckoutPayPal\Model\Payment\PayflowproCardDetails;
use Hyva\CheckoutPayPal\Model\Payment\PayflowproTransaction;
use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Paypal\Model\PayflowConfig;
use Magento\Quote\Api\Data\CartInterface;
use Magewirephp\Magewire\Component;

class Payflowpro extends Component implements EvaluationInterface
{
    public bool $transactionRequested = false;

    public bool $requestTokenInProgress = false;

    public bool $submitCardInProgress = false;

    public string $tokenResult = '';

    public string $tokenResultCode = '';

    public string $tokenMessage = '';

    public string $secureTokenId = '';

    public string $secureToken = '';

    /**
     * @var array
     */
    protected $loader = [
        'requestToken' => true,
        'checkTransaction' => true,
        'resetTransaction' => true
    ];

    /**
     * @var string[]
     */
    protected $listeners = ['payflowpro-payment-failed' => 'refresh'];

    private SessionCheckout $sessionCheckout;

    private PayflowproCardDetails $payflowproCardDetails;

    private PayflowproTransaction $payflowproTransaction;

    private CardDetailsFactory $cardDetailsFactory;

    private ValidateReCaptchaCommand $validateReCaptchaCommand;

    private string $methodCode;

    public function __construct(
        SessionCheckout $sessionCheckout,
        PayflowproCardDetails $payflowproCardDetails,
        PayflowproTransaction $payflowproTransaction,
        CardDetailsFactory $cardDetailsFactory,
        ValidateReCaptchaCommand $validateReCaptchaCommand,
        string $methodCode = PayflowConfig::METHOD_PAYFLOWPRO
    ) {
        $this->sessionCheckout = $sessionCheckout;
        $this->payflowproCardDetails = $payflowproCardDetails;
        $this->payflowproTransaction = $payflowproTransaction;
        $this->cardDetailsFactory = $cardDetailsFactory;
        $this->validateReCaptchaCommand = $validateReCaptchaCommand;
        $this->methodCode = $methodCode;
    }

    public function getMethodCode(): string
    {
        return $this->methodCode;
    }

    public function mount(): void
    {
        $this->transactionRequested = $this->payflowproTransaction->hasTransaction($this->getQuote());
        $this->prepareCardData();
    }

    public function boot(): void
    {
        $this->dispatchBrowserEvent('payflowpro-boot');
    }

    public function requestToken(
        string $cardType,
        string $cardLast4,
        string $expMonth,
        string $expYear,
        bool $shouldSaveVault,
        array $additionalData = []
    ): void {
        $this->requestTokenInProgress = true;

        try {
            $this->validateReCaptchaCommand->execute($additionalData);
        } catch (Exception $e) {
            $this->dispatchErrorMessage($e->getMessage());
            $this->emit('payflowpro-payment-failed');

            return;
        }

        $quote = $this->getQuote();

        try {
            $this->saveCardDetails($cardType, $cardLast4, $expMonth, $expYear, $shouldSaveVault);

            $token = $this->payflowproTransaction->requestToken($quote);
            $this->tokenMessage = $token->getData('respmsg');
            $this->tokenResult = $token->getData('result');
            $this->tokenResultCode = $token->getData('result_code');
            $this->secureTokenId = $token->getData('securetokenid');
            $this->secureToken = $token->getData('securetoken');

            $this->requestTokenInProgress = false;
            $this->submitCardInProgress = true;

            $this->dispatchBrowserEvent('payflowpro-token-generated');
        } catch (Exception $e) {
            $this->dispatchErrorMessage(__('Your payment has been declined. Please try again.'));
            $this->emit('payflowpro-payment-failed');
        }
    }

    public function checkTransaction(): void
    {
        if (!$this->payflowproTransaction->hasTransaction($this->getQuote())) {
            $this->resetState();
            $this->dispatchErrorMessage(__('Your payment has been declined. Please try again.'));
            $this->emit('payflowpro-payment-failed');
        } else {
            $this->submitCardInProgress = false;
        }

        $this->transactionRequested = true;
    }

    public function resetTransaction(): void
    {
        $this->payflowproTransaction->resetTransaction($this->getQuote());
        $this->resetCardDetails();

        $this->resetState();
    }

    public function evaluateCompletion(EvaluationResultFactory $resultFactory): EvaluationResultInterface
    {
        return !$this->transactionRequested ?
            $resultFactory->createErrorMessage()
                ->withMessage((string) __('Please authorize your card')) :
            $resultFactory->createSuccess();
    }

    private function resetCardDetails(): void
    {
        $this->payflowproCardDetails->resetCardDetails($this->getQuote());
    }

    private function saveCardDetails(
        string $cardType,
        string $cardLast4,
        string $expirationMonth,
        string $expirationYear,
        bool $shouldSaveVault
    ): void {
        $cardDetails = $this->cardDetailsFactory->create([
            'cardType' => $cardType,
            'cardLast4' => $cardLast4,
            'expirationMonth' => $expirationMonth,
            'expirationYear' => $expirationYear
        ]);

        $this->payflowproCardDetails->setCardDetails($this->getQuote(), $cardDetails, $shouldSaveVault);
    }

    private function prepareCardData(): void
    {
        $quote = $this->getQuote();
        $cardDetails = $this->payflowproCardDetails->getCardDetails($quote);

        if (!$this->transactionRequested) {
            if (!$cardDetails->hasEmptyData()) {
                $this->resetCardDetails();
            }

            return;
        }

        if ($cardDetails->hasEmptyData()) {
            $this->payflowproTransaction->resetTransaction($quote);
            $this->transactionRequested = false;
        }
    }

    private function resetState(): void
    {
        $this->submitCardInProgress = false;
        $this->requestTokenInProgress = false;
        $this->transactionRequested = false;
    }

    private function getQuote(): CartInterface
    {
        return $this->sessionCheckout->getQuote();
    }
}

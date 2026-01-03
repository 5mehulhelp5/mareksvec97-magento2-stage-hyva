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
use Hyva\CheckoutPayPal\Model\Payment\PayflowproStoredPayments;
use Magento\Framework\Exception\LocalizedException;
use Magento\Paypal\Model\PayflowConfig;
use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Paypal\Model\Payflow\Transparent as PayflowTransparent;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use Magewirephp\Magewire\Component;

class PayflowproCcVault extends Component implements EvaluationInterface
{
    public string $storedPaymentId = '';

    private SessionCheckout $sessionCheckout;

    private CartRepositoryInterface $cartRepository;

    private PayflowConfig $payflowConfig;

    private PayflowproStoredPayments $payflowproStoredPayments;

    private string $methodCode;

    public function __construct(
        SessionCheckout $sessionCheckout,
        CartRepositoryInterface $cartRepository,
        PayflowConfig $payflowConfig,
        PayflowproStoredPayments $payflowproStoredPayments,
        string $methodCode = PayflowTransparent::CC_VAULT_CODE
    ) {
        $this->sessionCheckout = $sessionCheckout;
        $this->cartRepository = $cartRepository;
        $this->payflowConfig = $payflowConfig;
        $this->payflowproStoredPayments = $payflowproStoredPayments;
        $this->methodCode = $methodCode;
    }

    public function getMethodCode(): string
    {
        return $this->methodCode;
    }

    /**
     * @return PaymentTokenInterface[]
     */
    public function getStorePayments(): array
    {
        return $this->payflowproStoredPayments->getCustomerTokens($this->getCustomerId());
    }

    public function isVaultEnabled(): bool
    {
        $quote = $this->sessionCheckout->getQuote();
        $this->payflowConfig->setMethodCode($this->getMethodCode());

        return $quote->getCustomerId() && $this->payflowConfig->getValue('active');
    }

    public function updatedStoredPaymentId(string $value): string
    {
        $selectedStoredPayment = $this->getSelectedStoredPayment($value);
        $value = $selectedStoredPayment ? $value : '';

        try {
            $quote = $this->sessionCheckout->getQuote();
            $payment = $quote->getPayment();

            $publicHash = $selectedStoredPayment ? $selectedStoredPayment->getPublicHash() : '';
            $payment->setAdditionalInformation(
                [
                    PaymentTokenInterface::CUSTOMER_ID => $this->getCustomerId(),
                    PaymentTokenInterface::PUBLIC_HASH => $publicHash
                ]
            );

            $this->cartRepository->save($quote);
        } catch (LocalizedException $exception) {
            $this->dispatchErrorMessage($exception->getMessage());
        }

        return $value;
    }

    public function evaluateCompletion(EvaluationResultFactory $resultFactory): EvaluationResultInterface
    {
        return $this->getSelectedStoredPayment($this->storedPaymentId) ?
            $resultFactory->createSuccess() :
            $resultFactory->createErrorMessage()
                ->withMessage((string) __('Credit card is not selected'));
    }

    private function getSelectedStoredPayment(string $storePaymentId): ?PaymentTokenInterface
    {
        $availablePayments = $this->getStorePayments();
        return $availablePayments[$storePaymentId] ?? null;
    }

    private function getCustomerId(): int
    {
        return (int) $this->sessionCheckout->getQuote()
            ->getCustomerId();
    }
}

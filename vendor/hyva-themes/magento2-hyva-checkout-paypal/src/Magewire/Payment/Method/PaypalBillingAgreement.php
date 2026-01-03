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
use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Framework\Exception\LocalizedException;
use Magento\Paypal\Model\Config as PaypalConfig;
use Magento\Paypal\Model\Billing\Agreement;
use Magento\Paypal\Model\Method\Agreement as AgreementMethod;
use Magento\Quote\Api\CartRepositoryInterface;
use Magewirephp\Magewire\Component;

class PaypalBillingAgreement extends Component implements EvaluationInterface
{
    public string $agreementId = '';

    private SessionCheckout $sessionCheckout;

    private CartRepositoryInterface $cartRepository;

    private BillingAgreement $billingAgreement;

    private string $methodCode;

    public function __construct(
        SessionCheckout $sessionCheckout,
        CartRepositoryInterface $cartRepository,
        BillingAgreement $billingAgreement,
        string $methodCode = PaypalConfig::METHOD_BILLING_AGREEMENT
    ) {
        $this->sessionCheckout = $sessionCheckout;
        $this->cartRepository = $cartRepository;
        $this->billingAgreement = $billingAgreement;
        $this->methodCode = $methodCode;
    }

    public function getMethodCode(): string
    {
        return $this->methodCode;
    }

    /**
     * @return Agreement[]
     */
    public function getAgreements(): array
    {
        $customerId = (int) $this->sessionCheckout->getQuote()
            ->getCustomerId();
        return $this->billingAgreement->getAgreements($customerId);
    }

    public function updatedAgreementId(string $agreementId): string
    {
        $selectedAgreement = $this->getSelectedAgreement($agreementId);
        $agreementId = $selectedAgreement ? $agreementId : '';

        try {
            $quote = $this->sessionCheckout->getQuote();
            $payment = $quote->getPayment();

            $referenceId = $selectedAgreement ? $selectedAgreement->getData('reference_id') : '';
            $payment->setAdditionalInformation(
                [
                    AgreementMethod::TRANSPORT_BILLING_AGREEMENT_ID => $agreementId,
                    AgreementMethod::PAYMENT_INFO_REFERENCE_ID => $referenceId,
                ]
            );

            $this->cartRepository->save($quote);
        } catch (LocalizedException $exception) {
            $this->dispatchErrorMessage($exception->getMessage());
        }

        return $agreementId;
    }

    public function evaluateCompletion(EvaluationResultFactory $resultFactory): EvaluationResultInterface
    {
        return $this->getSelectedAgreement($this->agreementId) ?
            $resultFactory->createSuccess() :
            $resultFactory->createErrorMessage()
                ->withMessage((string) __('Please select billing agreement'));
    }

    private function getSelectedAgreement(string $agreementId): ?Agreement
    {
        $agreements = $this->getAgreements();
        return $agreements[$agreementId] ?? null;
    }
}

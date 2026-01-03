<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */
declare(strict_types=1);

namespace Hyva\CheckoutPayPal\Model\Payment;

use Magento\Paypal\Model\Billing\AgreementFactory;
use Magento\Paypal\Model\Config as PayPalConfig;
use Magento\Paypal\Model\Billing\Agreement;
use Magento\Paypal\Model\Express\Checkout as PaypalExpressCheckout;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;

class BillingAgreement
{
    private AgreementFactory $agreementFactory;

    private PayPalConfig $payPalConfig;

    private CartRepositoryInterface $cartRepository;

    private PaypalExpressConfig $paypalExpressConfig;

    private array $agreementPayments;

    /**
     * @var Agreement[]
     */
    private array $customerAgreements = [];

    public function __construct(
        AgreementFactory $agreementFactory,
        PayPalConfig $payPalConfig,
        CartRepositoryInterface $cartRepository,
        PaypalExpressConfig $paypalExpressConfig,
        array $agreementPayments = [PaypalConfig::METHOD_WPP_EXPRESS]
    ) {
        $this->agreementFactory = $agreementFactory;
        $this->payPalConfig = $payPalConfig;
        $this->cartRepository = $cartRepository;
        $this->paypalExpressConfig = $paypalExpressConfig;
        $this->agreementPayments = $agreementPayments;
    }

    public function shouldAskBillingAgreement(int $customerId, string $methodCode): bool
    {
        if (!$customerId || !$this->canCreateAgreement($methodCode)) {
            return false;
        }

        $this->payPalConfig->setMethod($methodCode);

        return $this->payPalConfig->shouldAskToCreateBillingAgreement() &&
            $this->agreementFactory->create()->needToCreateForCustomer($customerId);
    }

    /**
     * @return Agreement[]
     */
    public function getAgreements(int $customerId): array
    {
        if (!$customerId) {
            return [];
        }

        if (!isset($this->customerAgreements[$customerId])) {
            $this->customerAgreements[$customerId] = $this->agreementFactory->create()
                ->getAvailableCustomerBillingAgreements($customerId)
                ->getItems();
        }

        return $this->customerAgreements[$customerId];
    }

    public function shouldCreateAgreement(CartInterface $quote): bool
    {
        $quotePayment = $quote->getPayment();
        $shouldCreate = (bool) $quotePayment->getAdditionalInformation(
            PaypalExpressCheckout::PAYMENT_INFO_TRANSPORT_BILLING_AGREEMENT
        );

        if (!$this->canCreateAgreement($quotePayment->getMethod())) {
            $this->setCreateAgreement($quote, false);
            return false;
        }

        return $shouldCreate;
    }

    public function setCreateAgreement(CartInterface $quote, bool $shouldCreate): void
    {
        $quotePayment = $quote->getPayment();

        if (!$this->canCreateAgreement($quotePayment->getMethod())) {
            $shouldCreate = false;
        }

        $quotePayment->setAdditionalInformation(
            PaypalExpressCheckout::PAYMENT_INFO_TRANSPORT_BILLING_AGREEMENT,
            $shouldCreate
        );

        $this->cartRepository->save($quote);
    }

    private function canCreateAgreement(string $methodCode): bool
    {
        return !$this->paypalExpressConfig->isInContext($methodCode) &&
            in_array($methodCode, $this->agreementPayments, true);
    }
}

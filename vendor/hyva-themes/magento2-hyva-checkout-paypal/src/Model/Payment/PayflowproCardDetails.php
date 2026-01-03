<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */
declare(strict_types=1);

namespace Hyva\CheckoutPayPal\Model\Payment;

use Hyva\CheckoutPayPal\Model\Data\CardDetails;
use Hyva\CheckoutPayPal\Model\Data\CardDetailsFactory;
use Magento\Paypal\Model\Payflowpro;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Api\PaymentMethodManagementInterface;
use Magento\Vault\Model\Ui\VaultConfigProvider;
use Magento\Paypal\Model\Payflow\Transparent;

class PayflowproCardDetails
{
    private CartRepositoryInterface $cartRepository;

    private PaymentMethodManagementInterface $paymentMethodManagement;

    private CardDetailsFactory $cardDetailsFactory;

    public function __construct(
        CartRepositoryInterface $cartRepository,
        PaymentMethodManagementInterface $paymentMethodManagement,
        CardDetailsFactory $cardDetailsFactory
    ) {
        $this->cartRepository = $cartRepository;
        $this->paymentMethodManagement = $paymentMethodManagement;
        $this->cardDetailsFactory = $cardDetailsFactory;
    }

    public function getShouldSaveVault(CartInterface $quote): bool
    {
        $quotePayment = $this->getPayment($quote);
        return (bool) $quotePayment->getAdditionalInformation(VaultConfigProvider::IS_ACTIVE_CODE);
    }

    public function setCardDetails(CartInterface $quote, CardDetails $cardDetails, bool $shouldSaveVault): void
    {
        $quotePayment = $this->getPayment($quote);

        $quotePayment->setAdditionalInformation(VaultConfigProvider::IS_ACTIVE_CODE, $shouldSaveVault);

        $cardData = [
            'cc_type' => $cardDetails->getCardType(),
            'cc_exp_year' => $cardDetails->getExpirationYear(),
            'cc_exp_month' => $cardDetails->getExpirationMonth(),
            'cc_last_4' => $cardDetails->getCardLast4()
        ];

        $quotePayment->addData($cardData);
        $quotePayment->setAdditionalInformation(Transparent::CC_DETAILS, $cardData);

        $this->cartRepository->save($quote);
    }

    public function getCardDetails(CartInterface $quote): CardDetails
    {
        $quotePayment = $this->getPayment($quote);
        $cardData = $quotePayment->getAdditionalInformation(Transparent::CC_DETAILS);

        return $this->cardDetailsFactory->create([
            'cardType' => (string) ($cardData['cc_type'] ?? ''),
            'cardLast4' => (string) ($cardData['cc_last_4'] ?? ''),
            'expirationMonth' => (string) ($cardData['cc_exp_month'] ?? ''),
            'expirationYear' => (string) ($cardData['cc_exp_year'] ?? '')
        ]);
    }

    public function resetCardDetails(CartInterface $quote): void
    {
        $quotePayment = $this->getPayment($quote);

        $cardData = ['cc_type' => '', 'cc_exp_year' => '', 'cc_exp_month' => '', 'cc_last_4' => ''];
        $quotePayment->addData($cardData);

        $quotePayment->unsAdditionalInformation(VaultConfigProvider::IS_ACTIVE_CODE);
        $quotePayment->unsAdditionalInformation(Transparent::CC_DETAILS);
        $quotePayment->unsAdditionalInformation(Payflowpro::PNREF);

        $this->cartRepository->save($quote);
    }

    private function getPayment(CartInterface $quote): PaymentInterface
    {
        return $this->paymentMethodManagement->get($quote->getId());
    }
}

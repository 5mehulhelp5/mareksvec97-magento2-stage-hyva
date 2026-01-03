<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */
declare(strict_types=1);

namespace Hyva\CheckoutPayPal\Model\Payment;

use Magento\Paypal\Model\PayflowConfig;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\Data\PaymentInterface;

class PaypalPaymentInformation
{
    private CartRepositoryInterface $cartRepository;

    private array $methodPaymentFields;

    public function __construct(
        CartRepositoryInterface $cartRepository,
        array $methodPaymentFields = []
    ) {
        $this->cartRepository = $cartRepository;
        $this->methodPaymentFields = $methodPaymentFields;
    }

    public function prepareAdditionalInformation(CartInterface $quote): void
    {
        $quotePayment = $quote->getPayment();

        $currentPaymentFields = $this->methodPaymentFields[$quotePayment->getMethod()] ?? null;

        if (is_array($currentPaymentFields)) {
            $this->clearOtherPaypalFields($quotePayment, $currentPaymentFields);
        } else {
            $this->clearAllPaypalFields($quotePayment);
        }

        if ($quotePayment->getMethod() !== PayflowConfig::METHOD_PAYFLOWPRO) {
            $cardData = ['cc_type' => '', 'cc_exp_year' => '', 'cc_exp_month' => '', 'cc_last_4' => ''];
            $quotePayment->addData($cardData);
        }

        $this->cartRepository->save($quote);
    }

    private function clearOtherPaypalFields(PaymentInterface $quotePayment, array $currentPaymentFields): void
    {
        $additionalInfo = $quotePayment->getAdditionalInformation() ?: [];

        foreach ($additionalInfo as $field => $value) {
            if (!in_array($field, $currentPaymentFields)) {
                $quotePayment->unsAdditionalInformation($field);
            }
        }
    }

    private function clearAllPaypalFields(PaymentInterface $quotePayment): void
    {
        foreach ($this->methodPaymentFields as $paymentFields) {
            array_map([$quotePayment, 'unsAdditionalInformation'], $paymentFields);
        }
    }
}

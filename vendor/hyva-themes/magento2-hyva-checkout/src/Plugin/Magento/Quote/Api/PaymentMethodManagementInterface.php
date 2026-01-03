<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */
declare(strict_types=1);

namespace Hyva\Checkout\Plugin\Magento\Quote\Api;

use Hyva\Checkout\Model\ConfigData\HyvaThemes\SystemConfigPayment;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\PaymentMethodInterface;
use Magento\Quote\Api\PaymentMethodManagementInterface as MagentoPaymentMethodManagementInterface;
use Psr\Log\LoggerInterface;

class PaymentMethodManagementInterface
{
    private SystemConfigPayment $hyvaCheckoutPaymentConfig;
    private CheckoutSession $checkoutSession;
    private LoggerInterface $logger;

    public function __construct(
        SystemConfigPayment $hyvaCheckoutPaymentConfig,
        CheckoutSession $checkoutSession,
        LoggerInterface $logger
    ) {
        $this->hyvaCheckoutPaymentConfig = $hyvaCheckoutPaymentConfig;
        $this->checkoutSession = $checkoutSession;
        $this->logger = $logger;
    }

    /**
     * Filter available payment methods for zero subtotal quotes.
     *
     * @param MagentoPaymentMethodManagementInterface $subject
     * @param array $result
     * @return array
     */
    public function afterGetList(
        MagentoPaymentMethodManagementInterface $subject,
        array $result
    ): array {
        try {
            if ($this->hyvaCheckoutPaymentConfig->removeNonZeroPaymentMethods()
                && ($quote = $this->checkoutSession->getQuote())
                && $quote->getGrandTotal() == 0 // deliberate loose comparison
            ) {
                $nonZeroPaymentMethodCodes = $this->hyvaCheckoutPaymentConfig->getEnabledZeroPaymentMethods();

                return array_filter($result, function ($paymentMethod) use ($nonZeroPaymentMethodCodes) {
                    /** @var $paymentMethod PaymentMethodInterface */
                    return in_array($paymentMethod->getCode(), $nonZeroPaymentMethodCodes);
                });
            }
        } catch (LocalizedException | NoSuchEntityException $e) {
            $this->logger->error(sprintf(
                'Error occurred while filtering payment method list for non-zero methods: %s',
                $e->getMessage()
            ));
        }

        return $result;
    }
}

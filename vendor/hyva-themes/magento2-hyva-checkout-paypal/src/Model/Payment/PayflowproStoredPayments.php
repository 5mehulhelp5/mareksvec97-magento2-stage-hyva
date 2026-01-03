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
use Magento\Vault\Api\Data\PaymentTokenFactoryInterface;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use Magento\Vault\Model\PaymentTokenManagement;

class PayflowproStoredPayments
{
    private PaymentTokenManagement $tokenManagement;

    /**
     * @var PaymentTokenInterface[]
     */
    private array $customerTokens = [];

    public function __construct(
        PaymentTokenManagement $tokenManagement
    ) {
        $this->tokenManagement = $tokenManagement;
    }

    /**
     * @return PaymentTokenInterface[]
     */
    public function getCustomerTokens(int $customerId): array
    {
        if ($customerId === 0) {
            return [];
        }

        if (!isset($this->customerTokens[$customerId])) {
            $customerTokens = $this->tokenManagement->getVisibleAvailableTokens($customerId);
            $this->customerTokens[$customerId] = array_filter($customerTokens, [$this, 'isAllowedToken']);
        }

        return $this->customerTokens[$customerId];
    }

    private function isAllowedToken(PaymentTokenInterface $paymentToken): bool
    {
        return $paymentToken->getPaymentMethodCode() === PayflowConfig::METHOD_PAYFLOWPRO &&
            $paymentToken->getType() === PaymentTokenFactoryInterface::TOKEN_TYPE_CREDIT_CARD;
    }
}

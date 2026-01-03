<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */
declare(strict_types=1);

namespace Hyva\CheckoutPayPal\Plugin\Hyva\Checkout\ViewModel\Checkout\Payment\MethodList;

use Hyva\Checkout\ViewModel\Checkout\Payment\MethodList;
use Hyva\CheckoutPayPal\Model\Payment\BillingAgreement;
use Hyva\CheckoutPayPal\Model\Payment\PayflowproStoredPayments;
use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Payment\Model\MethodInterface;
use Magento\Paypal\Model\Config as PaypalConfig;
use Magento\Paypal\Model\Payflow\Transparent as PayflowTransparent;

class ExcludePaypalMethods
{
    private array $excludedMethods;

    public function __construct(
        SessionCheckout $sessionCheckout,
        PayflowproStoredPayments $payflowproStoredPayments,
        BillingAgreement $billingAgreement,
        array $excludedMethods = [PaypalConfig::METHOD_WPP_BML]
    ) {
        $this->excludedMethods = $excludedMethods;

        $customerId = (int) $sessionCheckout->getQuote()
            ->getCustomerId();

        if (!$payflowproStoredPayments->getCustomerTokens($customerId)) {
            $this->excludedMethods[] = PayflowTransparent::CC_VAULT_CODE;
        }

        if (!$billingAgreement->getAgreements($customerId)) {
            $this->excludedMethods[] = PaypalConfig::METHOD_BILLING_AGREEMENT;
        }
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param MethodList $methodList
     * @param MethodInterface[]|null $methods
     * @return MethodInterface[]|null
     */
    public function afterGetList(MethodList $methodList, ?array $methods = null): ?array
    {
        if ($methods !== null) {
            $methods = array_filter(
                $methods,
                fn(MethodInterface $method) => !in_array($method->getCode(), $this->excludedMethods, true)
            );
        }

        return $methods;
    }
}

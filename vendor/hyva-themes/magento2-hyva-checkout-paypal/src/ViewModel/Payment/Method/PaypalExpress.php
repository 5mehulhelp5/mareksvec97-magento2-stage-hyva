<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */
declare(strict_types=1);

namespace Hyva\CheckoutPayPal\ViewModel\Payment\Method;

use Hyva\CheckoutPayPal\Model\Payment\BillingAgreement;
use Hyva\CheckoutPayPal\Model\Payment\PaypalExpressAuthorization;
use Hyva\CheckoutPayPal\Model\Payment\PaypalExpressConfig;
use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\QuoteIdToMaskedQuoteIdInterface;
use Magento\Paypal\Model\SmartButtonConfig;

class PaypalExpress implements ArgumentInterface
{
    private SessionCheckout $sessionCheckout;

    private PaypalExpressConfig $paypalExpressConfig;

    private PaypalExpressAuthorization $paypalExpressAuthorization;

    private SmartButtonConfig $smartButtonConfig;

    private BillingAgreement $billingAgreement;

    private QuoteIdToMaskedQuoteIdInterface $quoteIdToMaskedQuoteId;

    private UrlInterface $urlBuilder;

    private JsonSerializer $jsonSerializer;

    public function __construct(
        SessionCheckout $sessionCheckout,
        PaypalExpressConfig $paypalExpressConfig,
        PaypalExpressAuthorization $paypalExpressAuthorization,
        SmartButtonConfig $smartButtonConfig,
        BillingAgreement $billingAgreement,
        QuoteIdToMaskedQuoteIdInterface $quoteIdToMaskedQuoteId,
        UrlInterface $urlBuilder,
        JsonSerializer $jsonSerializer
    ) {
        $this->sessionCheckout = $sessionCheckout;
        $this->paypalExpressConfig = $paypalExpressConfig;
        $this->paypalExpressAuthorization = $paypalExpressAuthorization;
        $this->smartButtonConfig = $smartButtonConfig;
        $this->billingAgreement = $billingAgreement;
        $this->quoteIdToMaskedQuoteId = $quoteIdToMaskedQuoteId;
        $this->urlBuilder = $urlBuilder;
        $this->jsonSerializer = $jsonSerializer;
    }

    public function shouldAskBillingAgreement(string $methodCode): bool
    {
        $customerId = (int) $this->sessionCheckout->getQuote()
            ->getCustomerId();
        return $this->billingAgreement->shouldAskBillingAgreement($customerId, $methodCode);
    }

    public function isInContext(string $methodCode): bool
    {
        return $this->paypalExpressConfig->isInContext($methodCode);
    }

    public function getInContextJsConfig(CartInterface $quote): string
    {
        $quoteId = $quote->getCustomerIsGuest() ?
            $this->quoteIdToMaskedQuoteId->execute((int) $quote->getId()) :
            $quote->getId();

        $buttonConfig = array_replace_recursive(
            [
                'button' => 1,
                'token_url' => $this->urlBuilder->getUrl('paypal/express/getTokenData', ['_secure' => true]),
                'cancel_url' => $this->urlBuilder->getUrl('paypal/express/cancel', ['_secure' => true]),
                'review_url' => $this->urlBuilder->getUrl('checkout', ['_query' => ['step' => 'payment']]),
                'quote_id' => $quoteId,
                'customer_id' => $quote->getCustomerId(),
                'funding' => $this->paypalExpressAuthorization->getFundingSource($quote)
            ],
            $this->smartButtonConfig->getConfig('checkout')
        );

        return (string) $this->jsonSerializer->serialize($buttonConfig);
    }
}

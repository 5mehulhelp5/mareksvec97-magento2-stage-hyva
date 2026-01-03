<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */
declare(strict_types=1);

namespace Hyva\CheckoutPayPal\Model\Payment;

use Magento\Framework\UrlInterface;
use Magento\Paypal\Model\Config as PaypalConfig;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;

class PayflowIframeProvider
{
    private UrlInterface $urlBuilder;

    private array $methodsIframePaths;

    public function __construct(
        UrlInterface $urlBuilder,
        array $methodsIframePaths = [
            PaypalConfig::METHOD_PAYFLOWADVANCED => 'paypal/payflowadvanced/form',
            PaypalConfig::METHOD_PAYFLOWLINK => 'paypal/payflow/form',
            PaypalConfig::METHOD_HOSTEDPRO => 'paypal/hostedpro/redirect'
        ]
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->methodsIframePaths = $methodsIframePaths;
    }

    public function getAllowedMethods(): array
    {
        return array_keys($this->methodsIframePaths);
    }

    public function isIframeAllowed(OrderInterface $order): bool
    {
        $orderPayment = $order->getPayment();

        return $order->getId() &&
            $order->getState() === Order::STATE_PENDING_PAYMENT &&
            $orderPayment &&
            in_array($orderPayment->getMethod(), $this->getAllowedMethods(), true);
    }

    public function getIframeUrl(string $methodCode): string
    {
        $iframePath = $this->methodsIframePaths[$methodCode] ?? '';
        return $this->urlBuilder->getUrl($iframePath, ['_secure' => true]);
    }
}

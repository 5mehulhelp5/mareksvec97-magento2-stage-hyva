<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */
declare(strict_types=1);

namespace Hyva\CheckoutPayPal\ViewModel\Payment\Method;

use Hyva\CheckoutPayPal\Magewire\Payment\Method\PayflowproCcVault;
use Hyva\CheckoutPayPal\Model\Data\CardDetails;
use Hyva\CheckoutPayPal\Model\Payment\PayflowproCardDetails;
use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Payment\Model\CcConfigProvider;
use Magento\Paypal\Model\PayflowConfig;

class Payflowpro implements ArgumentInterface
{
    private SessionCheckout $sessionCheckout;

    private CcConfigProvider $iconsProvider;

    private PayflowproCcVault $payflowproCcVault;

    private PayflowConfig $payflowConfig;

    private PayflowproCardDetails $payflowproCardDetails;

    public function __construct(
        SessionCheckout $sessionCheckout,
        CcConfigProvider $iconsProvider,
        PayflowproCcVault $payflowproCcVault,
        PayflowConfig $payflowConfig,
        PayflowproCardDetails $payflowproCardDetails
    ) {
        $this->sessionCheckout = $sessionCheckout;
        $this->iconsProvider = $iconsProvider;
        $this->payflowproCcVault = $payflowproCcVault;
        $this->payflowConfig = $payflowConfig;
        $this->payflowproCardDetails = $payflowproCardDetails;
    }

    public function isVaultEnabled(): bool
    {
        return $this->payflowproCcVault->isVaultEnabled();
    }

    public function getCardDetails(): CardDetails
    {
        return $this->payflowproCardDetails->getCardDetails($this->sessionCheckout->getQuote());
    }

    public function shouldSaveCustomerVault(): bool
    {
        return $this->payflowproCardDetails->getShouldSaveVault($this->sessionCheckout->getQuote());
    }

    public function getCgiUrl(string $methodCode): string
    {
        $this->payflowConfig->setMethodCode($methodCode);

        if ($this->payflowConfig->getValue('sandbox_flag')) {
            return $this->payflowConfig->getValue('cgi_url_test_mode');
        }

        return $this->payflowConfig->getValue('cgi_url');
    }

    public function getCardIconData(string $ccType): array
    {
        return $this->iconsProvider->getIcons()[$ccType] ?? [];
    }
}

<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Plugin\Magento\Checkout\Controller\Index;

use Hyva\Checkout\Model\CheckoutInformationProvider;
use Hyva\Checkout\Model\Config as HyvaCheckoutConfig;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\ResultFactory;
use Magento\Checkout\Controller\Index\Index as Subject;
use Hyva\Checkout\Model\ConfigData\HyvaThemes\Checkout as SystemCheckoutConfig;

class Index
{
    protected ResultFactory $resultFactory;
    protected ScopeConfigInterface $scopeConfig;
    protected SystemCheckoutConfig $systemCheckoutConfig;
    protected CheckoutInformationProvider $checkoutInformationProvider;
    protected HyvaCheckoutConfig $hyvaCheckoutConfig;

    public function __construct(
        ResultFactory $resultFactory,
        ScopeConfigInterface $scopeConfig,
        SystemCheckoutConfig $systemCheckoutConfig,
        HyvaCheckoutConfig $hyvaCheckoutConfig,
        CheckoutInformationProvider $checkoutInformationProvider
    ) {
        $this->resultFactory = $resultFactory;
        $this->scopeConfig = $scopeConfig;
        $this->systemCheckoutConfig = $systemCheckoutConfig;
        $this->checkoutInformationProvider = $checkoutInformationProvider;
        $this->hyvaCheckoutConfig = $hyvaCheckoutConfig;
    }

    public function aroundExecute(Subject $subject, callable $proceed)
    {
        $namespaceActiveCheckout = $this->hyvaCheckoutConfig->getActiveCheckoutNamespace();

        $checkoutList = $this->checkoutInformationProvider->getList();
        $checkout = $checkoutList[$namespaceActiveCheckout] ?? null;
        $this->hyvaCheckoutConfig->isHyvaCheckout($namespaceActiveCheckout);

        if ($checkout
            && $checkout->canApply()
            && ! $this->hyvaCheckoutConfig->isHyvaCheckout($namespaceActiveCheckout)) {
            return $checkout->execute($proceed);
        }

        $result = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
        $result->setModule('hyva_checkout')->setController('index');

        return $result->forward('index');
    }
}

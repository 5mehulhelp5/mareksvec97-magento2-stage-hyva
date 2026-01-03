<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\CheckoutInformation;

use Hyva\Checkout\Model\CheckoutInformationInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Makes the OneStepCheckout checkout compatible and usable when installed and selected.
 */
class OneStepCheckout implements CheckoutInformationInterface
{
    public const NAMESPACE = 'onestepcheckout_iosc';

    protected ScopeConfigInterface $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function getNamespace(): string
    {
        return self::NAMESPACE;
    }

    public function getLabel(): string
    {
        return 'One Step Checkout (original)';
    }

    public function canApply(): bool
    {
        return $this->scopeConfig->isSetFlag('onestepcheckout_iosc/general/enable', ScopeInterface::SCOPE_STORE);
    }

    public function execute(callable $checkout)
    {
        return $checkout();
    }
}

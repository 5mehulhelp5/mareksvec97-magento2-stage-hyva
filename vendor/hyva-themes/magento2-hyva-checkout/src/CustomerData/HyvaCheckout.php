<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\CustomerData;

use Hyva\Checkout\Model\Config as CheckoutConfig;
use Magento\Customer\CustomerData\SectionSourceInterface;

class HyvaCheckout implements SectionSourceInterface
{
    private CheckoutConfig $checkoutConfig;

    public function __construct(CheckoutConfig $checkoutConfig)
    {
        $this->checkoutConfig = $checkoutConfig;
    }

    public function getSectionData()
    {
        return [
            'active_namespace' => $this->checkoutConfig->getActiveCheckoutNamespace()
        ];
    }
}

<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */
declare(strict_types=1);

namespace Hyva\CheckoutPayPal\Plugin\Paypal\Controller\Express\AbstractExpress;

use Hyva\Checkout\Model\Config as HyvaCheckoutConfig;

class AbstractPayPalExpressPlugin
{
    protected HyvaCheckoutConfig $hyvaCheckoutConfig;

    public function __construct(
        HyvaCheckoutConfig $hyvaCheckoutConfig
    ) {
        $this->hyvaCheckoutConfig = $hyvaCheckoutConfig;
    }

    public function isMultiStepCheckout(): bool
    {
        $activeHyvaCheckout = $this->hyvaCheckoutConfig->getActiveCheckoutData();

        return array_key_exists('steps', $activeHyvaCheckout)
            && count($activeHyvaCheckout['steps']) > 1;
    }
}

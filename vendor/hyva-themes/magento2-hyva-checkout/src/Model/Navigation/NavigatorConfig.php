<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Navigation;

use Hyva\Checkout\Model\Config as SystemConfig;

class NavigatorConfig
{
    private SystemConfig $systemConfig;

    public function __construct(
        SystemConfig $systemConfig
    ) {
        $this->systemConfig = $systemConfig;
    }

    public function getSystem(): SystemConfig
    {
        return $this->systemConfig;
    }

    public function getDefaultCheckout(): string
    {
        return $this->getSystem()->getActiveCheckoutNamespace();
    }
}

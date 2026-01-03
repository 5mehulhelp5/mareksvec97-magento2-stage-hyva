<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Plugin\Magento\Checkout\CustomerData;

use Hyva\Checkout\Model\ConfigData\HyvaThemes\SystemConfigGeneral;
use Magento\Checkout\CustomerData\Cart as CartSectionData;

class AddHyvaCheckoutPlugin
{
    private SystemConfigGeneral $generalConfig;

    public function __construct(
        SystemConfigGeneral $generalConfig
    ) {
        $this->generalConfig = $generalConfig;
    }

    public function afterGetSectionData(CartSectionData $subject, array $result): array
    {
        $result['hyva_checkout_enabled'] = $this->generalConfig->getCheckout() !== 'magento_luma';

        return $result;
    }
}

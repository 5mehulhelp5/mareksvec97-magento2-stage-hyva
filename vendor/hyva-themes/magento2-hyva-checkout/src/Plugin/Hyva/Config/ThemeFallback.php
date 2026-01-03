<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Plugin\Hyva\Config;

use Hyva\Checkout\Model\CheckoutInformationProvider;
use Hyva\Checkout\Model\ConfigData\HyvaThemes\SystemConfigGeneral;
use Hyva\ThemeFallback\Config\ThemeFallback as Subject;

class ThemeFallback
{
    public function __construct(
        private readonly CheckoutInformationProvider $checkoutInformationProvider,
        private readonly SystemConfigGeneral $systemConfigGeneral
    ) {
        //
    }

    public function afterGetListPartOfUrl(Subject $subject, array $result): array
    {
        if (! in_array('checkout/index', $result)) {
            return $result;
        }

        foreach ($this->checkoutInformationProvider->getList() as $checkoutInfo) {
            if ($checkoutInfo->canApply()
                && $this->systemConfigGeneral->getCheckout() === $checkoutInfo->getNamespace()) {
                return $result;
            }
        }

        return array_diff($result, ['checkout/index']);
    }
}

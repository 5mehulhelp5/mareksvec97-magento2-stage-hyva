<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */
declare(strict_types=1);

namespace Hyva\CheckoutPayPal\Model;

use Hyva\Checkout\Model\Config as HyvaCheckoutConfig;
use Hyva\Checkout\Model\ConfigData\HyvaThemes\SystemConfigGeneral as HyvaSystemConfigGeneral;
use Magento\Framework\Exception\NotFoundException;

class CheckoutType
{
    private HyvaSystemConfigGeneral $hyvaSystemConfigGeneral;

    private HyvaCheckoutConfig $hyvaCheckoutConfig;

    private ?bool $isHyvaCheckout = null;

    public function __construct(
        HyvaSystemConfigGeneral $hyvaSystemConfigGeneral,
        HyvaCheckoutConfig $hyvaCheckoutConfig
    ) {
        $this->hyvaSystemConfigGeneral = $hyvaSystemConfigGeneral;
        $this->hyvaCheckoutConfig = $hyvaCheckoutConfig;
    }

    public function isHyvaCheckout(): bool
    {
        if ($this->isHyvaCheckout !== null) {
            return $this->isHyvaCheckout;
        }

        $currentCheckout = $this->hyvaSystemConfigGeneral->getCheckout();
        $hyvaCheckoutTypes = $this->getHyvaCheckoutTypes();

        $this->isHyvaCheckout = in_array($currentCheckout, $hyvaCheckoutTypes, true);

        return $this->isHyvaCheckout;
    }

    private function getHyvaCheckoutTypes(): array
    {
        try {
            return array_column($this->hyvaCheckoutConfig->getList(), 'name');
        } catch (NotFoundException $e) {
            return [];
        }
    }
}

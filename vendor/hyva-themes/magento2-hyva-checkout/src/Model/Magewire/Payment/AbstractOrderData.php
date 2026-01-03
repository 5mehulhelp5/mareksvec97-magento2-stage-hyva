<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Magewire\Payment;

use Magento\Framework\DataObject;

abstract class AbstractOrderData extends DataObject
{
    public function getPayment(): array
    {
        return $this->getData('payment') ?? [];
    }

    public function getShipping(): array
    {
        return $this->getData('shipping') ?? [];
    }
}

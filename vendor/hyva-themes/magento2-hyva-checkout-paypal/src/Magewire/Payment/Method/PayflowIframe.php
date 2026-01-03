<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */
declare(strict_types=1);

namespace Hyva\CheckoutPayPal\Magewire\Payment\Method;

use Magewirephp\Magewire\Component;

class PayflowIframe extends Component
{
    private string $methodCode;

    public function __construct(string $methodCode)
    {
        $this->methodCode = $methodCode;
    }

    public function getMethodCode(): string
    {
        return $this->methodCode;
    }
}

<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Magewire\ServerMemoConfig;

class SessionStorage extends AbstractConfigSection
{
    public function getData(): array
    {
        return [
            'payment'  => [],
            'shipping' => []
        ];
    }

    public function isStaticData(): bool
    {
        return false;
    }
}

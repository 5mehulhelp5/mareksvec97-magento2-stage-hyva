<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Magewire\Component\Evaluation;

class Success extends Event
{
    public const EVENT = 'evaluation:event:success';

    public function canDispatch(): bool
    {
        return true;
    }
}

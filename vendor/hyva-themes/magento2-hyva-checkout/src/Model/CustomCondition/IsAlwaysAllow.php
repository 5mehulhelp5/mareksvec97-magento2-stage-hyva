<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\CustomCondition;

use Hyva\Checkout\Model\CustomConditionInterface;

/**
 * @api
 */
class IsAlwaysAllow implements CustomConditionInterface
{
    public function validate(): bool
    {
        return true;
    }
}

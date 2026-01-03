<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model;

/**
 * @api
 */
interface CustomConditionInterface
{
    public const DEFAULT_METHOD = 'validate';

    /**
     * Validate custom condition.
     *
     * @return bool
     */
    public function validate(): bool;
}

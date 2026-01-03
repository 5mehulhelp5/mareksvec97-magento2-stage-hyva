<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Magewire;

/**
 * @api
 */
interface ServerMemoConfigInterface
{
    /**
     * @return array
     */
    public function getData(): array;

    /**
     * Determines if the config data needs to be refreshed during subsequent requests.
     *
     * @return bool
     */
    public function isStaticData(): bool;
}

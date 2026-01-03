<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Magewire\Component;

/**
 * @api
 */
interface MainInterface
{
    public const METHOD_NAVIGATE = 'navigateToStep';
    public const METHOD_PLACE_ORDER = 'placeOrder';

    /**
     * @param string $route
     */
    public function navigateToStep(string $route): void;

    /**
     * @return void
     */
    public function placeOrder(): void;
}

<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Magewire;

use Magewirephp\Magewire\Model\RequestInterface;

interface UpdateAdapterInterface
{
    /**
     * Validate if the current AddressMagewire component is of type 'hyva-checkout-main'.
     */
    public function belongsToNavigationComponent(RequestInterface $request): bool;

    /**
     * Validate if the current AddressMagewire update is of type context switch.
     */
    public function isNavigationUpdateRequest(array $update): bool;

    /**
     * Locate the required step based on the given AddressMagewire update item.
     */
    public function locateStep(array $update): ?string;
}

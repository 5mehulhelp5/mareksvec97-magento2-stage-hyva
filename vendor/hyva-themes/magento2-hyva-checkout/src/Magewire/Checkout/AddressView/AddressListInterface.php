<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Magewire\Checkout\AddressView;

interface AddressListInterface
{
    public function activateAddress(string $id): bool;

    public function updatingActiveAddressEntity(string $id): int;
}

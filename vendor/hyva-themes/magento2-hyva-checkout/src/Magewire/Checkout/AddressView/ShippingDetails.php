<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Magewire\Checkout\AddressView;

use Magewirephp\Magewire\Component;

class ShippingDetails extends Component
{
    protected $listeners = [
        'shipping_address_submitted' => 'refresh',
        'shipping_address_added' => 'refresh'
    ];
}

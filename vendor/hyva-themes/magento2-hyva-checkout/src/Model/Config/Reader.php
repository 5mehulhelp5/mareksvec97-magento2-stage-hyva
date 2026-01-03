<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Config;

use Magento\Framework\Config\Reader\Filesystem;

class Reader extends Filesystem
{
    /** @var string[] $_idAttributes */
    protected $_idAttributes = [
        '/config/checkout' => 'name',
        '/config/checkout/update' => 'handle',
        '/config/checkout/step' => 'name',
        '/config/checkout/step/update' => 'handle',
        // Group elements when both have a name: '/config/checkout/(condition|something-else)' => ...
        '/config/checkout/step/condition' => 'name',
    ];
}

<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\App\Cache\Manager as CacheManager;
use Hyva\Checkout\Model\Cache\Type\HyvaCheckout as CheckoutCache;

class EnableCache implements DataPatchInterface
{
    protected CacheManager $cacheManager;

    public function __construct(
        CacheManager $cacheManager
    ) {
        $this->cacheManager = $cacheManager;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }

    public function apply(): self
    {
        $types = [CheckoutCache::TYPE_IDENTIFIER];
        $availableTypes = $this->cacheManager->getAvailableTypes();
        $types = array_intersect($availableTypes, $types);
        $enabledTypes = $this->cacheManager->setEnabled($types, true);
        $this->cacheManager->clean($enabledTypes);

        return $this;
    }
}

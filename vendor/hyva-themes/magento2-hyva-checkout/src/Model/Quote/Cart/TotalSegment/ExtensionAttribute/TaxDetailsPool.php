<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Quote\Cart\TotalSegment\ExtensionAttribute;

/**
 * A dedicated item pool that encompasses all available extension attributes for total segment tax details.
 */
class TaxDetailsPool
{
    protected array $taxDetailsItems;

    public function __construct(
        array $taxDetailsItems = []
    ) {
        $this->taxDetailsItems = $taxDetailsItems;
    }

    public function getTaxDetailsExtensionAttributeItem(string $taxDetailsItemName): ?AbstractTaxDetailsExtensionAttribute
    {
        return $this->taxDetailsItems[$taxDetailsItemName] ?? null;
    }

    public function hasTaxDetailsExtensionAttributeItem(string $taxDetailsItemName): bool
    {
        return array_key_exists($taxDetailsItemName, $this->taxDetailsItems);
    }
}

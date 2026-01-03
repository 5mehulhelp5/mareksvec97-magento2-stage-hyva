<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Api\Data\Quote;

interface TaxDetailsExtensionAttributeInterface
{
    /**
     * @return float
     */
    public function getInclTaxValue(): float;

    /**
     * @return float
     */
    public function getExclTaxValue(): float;

    /**
     * @return bool
     */
    public function showExclTaxPrice(): bool;

    /**
     * @return bool
     */
    public function showInclTaxPrice(): bool;

    /**
     * @return \Magento\Quote\Api\Data\TotalsInterface
     */
    public function getQuoteTotals(): \Magento\Quote\Api\Data\TotalsInterface;

    /**
     * @return float
     */
    public function getTaxRate(): float;
}

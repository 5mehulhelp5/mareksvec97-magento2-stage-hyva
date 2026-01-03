<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Quote\Cart\TotalSegment\ExtensionAttribute;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class ShippingTaxDetailsExtensionAttribute extends AbstractTaxDetailsExtensionAttribute
{
    public const SEGMENT_CODE_EXCL_VAT = 'shipping_amount';
    public const SEGMENT_CODE_INCL_VAT = 'shipping_incl_tax';

    /**
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getInclTaxValue(): float
    {
        return $this->getItemTotalByCode(self::SEGMENT_CODE_INCL_VAT);
    }

    /**
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getExclTaxValue(): float
    {
        return $this->getItemTotalByCode(self::SEGMENT_CODE_EXCL_VAT);
    }
}

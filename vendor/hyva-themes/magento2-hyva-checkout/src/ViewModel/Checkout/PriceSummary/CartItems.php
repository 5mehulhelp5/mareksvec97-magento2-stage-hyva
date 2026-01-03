<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\ViewModel\Checkout\PriceSummary;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\Element\Template;

class CartItems implements ArgumentInterface
{
    /**
     * Checks whether a specific product type renderer is available and returns it,
     * or returns the default renderer if the specified renderer is not available.
     *
     * @return false|AbstractBlock
     */
    public function getProductOptionsRenderer(Template $parent, string $productType)
    {
        $productTypeRenderersBlock = $parent->getChildBlock('product-type-renderers');

        if (! $productTypeRenderersBlock) {
            return false;
        }

        $optionsRendererBlock = $productTypeRenderersBlock->getChildBlock($productType);
        return $optionsRendererBlock ?: $productTypeRenderersBlock->getChildBlock('default');
    }
}

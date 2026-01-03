<?php
/**
 * Copyright © MagePal LLC. All rights reserved.
 * See COPYING.txt for license details.
 * http://www.magepal.com | support@magepal.com
 */

namespace MagePal\GoogleAnalytics4\Helper;

use Magento\Quote\Model\Quote\Item;

class DataLayerItem extends \MagePal\GoogleTagManager\Helper\DataLayerItem
{

    /**
     * @param  Item $item
     * @param $qty
     * @return array
     */
    public function getProductObject($item, $qty)
    {
        $viewItem = [
            'item_name' => $item->getName(),
            'item_id' => $item->getSku(),
            'id' => $item->getSku(),
            'price' => $this->formatPrice($item->getPrice()),
            'quantity' => $qty * 1,
            'parent_sku' => $item->getProduct() ? $item->getProduct()->getData('sku') : $item->getSku(),
            'currency' => $item->getQuote()->getStore()->getCurrentCurrency()->getCode()
        ];

        if ($item->getItemId()) {
            $viewItem['quote_item_id'] = $item->getItemId();
        }

        if (!$item->getPrice() && $item->getProduct()) {
            $viewItem['price'] =  $this->formatPrice($item->getProduct()->getPrice());
        }

        if ($variant = $this->getItemVariant($item)) {
            $viewItem['item_variant'] = $variant;
        }

        if ($item->getDiscountAmount()) {
            $viewItem['discount'] = $this->formatPrice($item->getDiscountAmount());
        }

        $this->addCategoryElements($item->getProduct(), $viewItem);

        return $viewItem;
    }
}

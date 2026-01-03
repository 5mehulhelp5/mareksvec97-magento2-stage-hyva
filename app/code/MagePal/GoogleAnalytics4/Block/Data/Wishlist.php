<?php
/**
 * Copyright © MagePal LLC. All rights reserved.
 * See COPYING.txt for license details.
 * https://www.magepal.com | support@magepal.com
 */

namespace MagePal\GoogleAnalytics4\Block\Data;

use ArrayIterator;
use Magento\Framework\Exception\LocalizedException;
use MagePal\GoogleAnalytics4\Block\CatalogLayer;
use MagePal\GoogleTagManager\Model\DataLayerEvent;

class Wishlist extends CatalogLayer
{
    /**
     * Add category data to datalayer
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function _dataLayer()
    {
        if ($list = $this->_eeHelper->getWishListItemListName()) {
            $this->setItemListName($list);
        }

        $collection = $this->_getProductCollection();

        if (is_object($collection) && $collection->count()) {
            $itemCollection = [];

            //convert wishlist collection
            foreach ($collection as $item) {
                if ($item->getProduct()) {
                    $itemCollection[] = $item->getProduct();
                }
            }

            $wishlistCollection = new ArrayIterator($itemCollection);

            $products = $this->getProductImpressions($wishlistCollection);

            $this->setImpressionList(
                $this->getItemListName(),
                $this->_eeHelper->getWishListClassName(),
                $this->_eeHelper->getWishListContainerClass()
            );

            $impressionsData = [
                'event' => DataLayerEvent::GA4_VIEW_ITEM_LIST,
                'ecommerce' => [
                    'items' => $products,
                    'currency' => $this->getStoreCurrencyCode(),
                    'item_list_name' => $this->getItemListName(),
                    'item_list_id'=> $this->getItemListId()
                ],
                '_clear' => true
            ];

            $this->addCustomDataLayerByEvent(DataLayerEvent::GA4_VIEW_ITEM_LIST, $impressionsData);
        }

        return $this;
    }

    protected function isRelatedOrCrosssell()
    {
        return true;
    }
}

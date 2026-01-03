<?php
/**
 * Copyright © MagePal LLC. All rights reserved.
 * See COPYING.txt for license details.
 * https://www.magepal.com | support@magepal.com
 */

namespace MagePal\GoogleAnalytics4\Block\Data;

use Magento\Framework\Exception\LocalizedException;
use MagePal\GoogleAnalytics4\Block\CatalogLayer;
use MagePal\GoogleTagManager\Model\DataLayerEvent;

class Category extends CatalogLayer
{

    /**
     * Add category data to datalayer
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function _dataLayer()
    {
        if ($this->_eeHelper->isItemListCategoryName() && $categoryName = $this->getCurrentCategoryName()) {
            $this->setItemListName($categoryName);
        } elseif ($list = $this->_eeHelper->getCategoryItemListName()) {
            $this->setItemListName($list);
        }

        $collection = $this->_getProducts();

        if (is_object($collection) && $collection->count()) {
            $this->setImpressionList(
                $this->getItemListName(),
                $this->_eeHelper->getCategoryListClassName(),
                $this->_eeHelper->getCategoryListContainerClass()
            );

            $products = $this->getProductImpressions($collection);

            $impressionsData = [
                'event' => DataLayerEvent::GA4_VIEW_ITEM_LIST,
                'ecommerce' => [
                    'items' => $products,
                    'currency' => $this->getStoreCurrencyCode()
                ],
                '_clear' => true
            ];

            $this->addCustomDataLayerByEvent(DataLayerEvent::GA4_VIEW_ITEM_LIST, $impressionsData);
        }

        return $this;
    }
}

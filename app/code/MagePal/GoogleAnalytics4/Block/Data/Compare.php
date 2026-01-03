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

class Compare extends CatalogLayer
{
    /**
     * Add category data to datalayer
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function _dataLayer()
    {
        if ($list = $this->_eeHelper->getCompareItemListName()) {
            $this->setItemListName($list);
        }

        $collection = $this->_getProductCollection();

        if (is_object($collection) && $collection->count()) {
            $products = $this->getProductImpressions($collection);

            $this->setImpressionList(
                $this->getItemListName(),
                $this->_eeHelper->getCompareListClassName(),
                $this->_eeHelper->getCompareListContainerClass()
            );

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

    protected function isRelatedOrCrosssell()
    {
        return true;
    }
}

<?php
/**
 * Copyright © MagePal LLC. All rights reserved.
 * See COPYING.txt for license details.
 * https://www.magepal.com | support@magepal.com
 */

namespace MagePal\GoogleAnalytics4\Block\Data;

use Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection;
use Magento\Framework\Exception\LocalizedException;
use MagePal\GoogleAnalytics4\Block\CatalogLayer;
use MagePal\GoogleTagManager\Model\DataLayerEvent;

/**
 * @method getCatalogWidgetBlockList()
 */
class CatalogWidget extends CatalogLayer
{

    /**
     * Add category data to datalayer
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function _dataLayer()
    {
        if (is_array($this->getCatalogWidgetBlockList())) {
            $allBlocks = [];

            foreach ($this->getCatalogWidgetBlockList() as $_block) {
                $allBlocks[] = $this->getLayout()->getBlock($_block);
            }
        } else {
            $allBlocks = $this->getLayout()->getAllBlocks();
        }

        $itemListName = $this->getItemListName();

        $regex = "/" . preg_quote('product\productslist_', '/') . "\d+/";

        $blockIncrement = 1;
        foreach ($allBlocks as $block) {
            if (preg_match($regex, $block->getNameInLayout())) {
                $this->setBlockName($block->getNameInLayout());

                if ($block->getTitle() || $this->getUseWidgetTitle()) {
                    $this->setItemListName($itemListName . ' ' . $block->getTitle());
                } elseif ($blockIncrement > 1) {
                    $this->setItemListName($itemListName . ' ' . ($blockIncrement++));
                } else {
                    $this->setItemListName($itemListName);
                }

                //bypass category display mode
                $collection = $this->_getProductCollection(true);

                if (is_object($collection) && $collection->count()) {
                    $products = $this->getProductImpressions($collection);

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

                    $this->addImpressionList();

                    $this->addCustomDataLayerByEvent(DataLayerEvent::GA4_VIEW_ITEM_LIST, $impressionsData);
                }
            }
        }

        return $this;
    }

    public function addImpressionList()
    {
        $this->setImpressionList(
            $this->getItemListName(),
            $this->_eeHelper->getCategoryWidgetClassName(),
            $this->_eeHelper->getCategoryWidgetContainerClass()
        );
    }

    protected function _init()
    {
        $this->setItemListName($this->_eeHelper->getCategoryWidgetItemListName());
        $this->setUseWidgetTitle($this->_eeHelper->getCategoryWidgetUseWidgetTitle());
        return $this;
    }

    /**
     * Retrieve loaded category collection
     *
     * @param bool $reload
     * @return AbstractCollection | null
     * @throws LocalizedException
     */
    protected function _getProductCollection($reload = false)
    {
        if ($reload === true) {
            $this->_productCollection = null;
        }

        /* For catalog list and search results
         * Expects getListBlock as \Magento\Catalog\Block\Product\ListProduct
         */
        if (null === $this->_productCollection) {
            if ($block = $this->getListBlock()) {
                $this->_productCollection = $block->getProductCollection();
            }
        }

        return $this->_productCollection;
    }
}

<?php
/**
 * Copyright © MagePal LLC. All rights reserved.
 * See COPYING.txt for license details.
 * https://www.magepal.com | support@magepal.com
 */

namespace MagePal\GoogleAnalytics4\Block\Data;

use Exception;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use MagePal\GoogleAnalytics4\Block\CatalogLayer;
use MagePal\GoogleTagManager\Model\DataLayerEvent;

class Product extends CatalogLayer
{
    const TYPE_GROUP = 'grouped';
    const TYPE_SIMPLE = 'simple';
    const TYPE_BUNDLE = 'bundle';
    const TYPE_VIRTUAL = 'virtual';
    const TYPE_CONFIGURABLE = 'configurable';

    /**
     * Add category data to datalayer
     *
     * @return $this
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function _dataLayer()
    {
        /** @var $currentProduct ProductInterface */
        $currentProduct = $this->getProduct();

        if ($currentProduct) {
            switch ($currentProduct->getTypeId()) {
                case static::TYPE_GROUP:
                    $products = $this->getGroupProducts();
                    $products[] = $this->getProductLayer($currentProduct);
                    break;
                case static::TYPE_CONFIGURABLE:
                    $products = $this->getConfigurableProducts();
                    $products[] = $this->getProductLayer($currentProduct);
                    break;
                case static::TYPE_BUNDLE:
                    $products = $this->getBundleProducts();
                    $products[] = $this->getProductLayer($currentProduct);
                    break;
                default:
                    $products[] = $this->getProductLayer($currentProduct);
            }

            $impressionsProductData = [
                'event' => DataLayerEvent::GA4_VIEW_ITEM,
                'ecommerce' => [
                    'items' => $products,
                    'currency' => $this->getStoreCurrencyCode(),
                    'value' => $this->_gtmHelper->getProductPrice($currentProduct)
                ],
                '_clear' => true
            ];

            $this->addCustomDataLayerByEvent(DataLayerEvent::GA4_VIEW_ITEM, $impressionsProductData);

            $relatedProduct = $this->getRelatedProduct();

            if (!empty($relatedProduct)) {
                $impressionsListData = [
                    'event' => DataLayerEvent::GA4_VIEW_ITEM_LIST,
                    'ecommerce' => [
                        'items' => $relatedProduct,
                        'currency' => $this->getStoreCurrencyCode(),
                        'item_list_name' => $this->getItemListName(),
                        'item_list_id'=> $this->getItemListId()
                    ],
                    '_clear' => true
                ];

                $this->addCustomDataLayer($impressionsListData, 21);

                $this->setImpressionList(
                    $this->_eeHelper->getRelatedItemListName(),
                    $this->_eeHelper->getRelatedClassName(),
                    $this->_eeHelper->getRelatedContainerClass()
                );
            }

            $upsellProduct = $this->getUpsellProduct();
            if (!empty($upsellProduct)) {
                $impressionsListData = [
                    'event' => DataLayerEvent::GA4_VIEW_ITEM_LIST,
                    'ecommerce' => [
                        'items' => $upsellProduct,
                        'currency' => $this->getStoreCurrencyCode(),
                        'item_list_name' => $this->getItemListName(),
                        'item_list_id'=> $this->getItemListId()
                    ],
                    '_clear' => true
                ];

                $this->addCustomDataLayer($impressionsListData, 22);

                $this->setImpressionList(
                    $this->_eeHelper->getUpsellItemListName(),
                    $this->_eeHelper->getUpsellClassName(),
                    $this->_eeHelper->getUpsellContainerClass()
                );
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    protected function getGroupProducts()
    {
        $product = $this->getProduct();
        $products = [];

        if ($product) {
            $associatedProducts = $product->getTypeInstance()->getAssociatedProducts($product);

            foreach ($associatedProducts as $associatedProduct) {
                $products[] = $this->getProductLayer($associatedProduct);
            }
        }

        return $products;
    }

    /**
     * @return array
     */
    protected function getBundleProducts()
    {
        $products = [];

        if ($product = $this->getProduct()) {
            $associatedProducts = $product->getTypeInstance()->getSelectionsCollection(
                $product->getTypeInstance()->getOptionsIds($product),
                $product
            );

            foreach ($associatedProducts as $associatedProduct) {
                $products[] = $this->getProductLayer($associatedProduct);
            }
        }

        return $products;
    }

    /**
     * @return array
     */
    protected function getConfigurableProducts()
    {
        $product = $this->getProduct();
        $products = [];

        if ($product) {
            $configProducts = $product->getTypeInstance()->getUsedProducts($product);

            foreach ($configProducts as $configProduct) {
                $products[] = $this->getProductLayer($configProduct);
            }
        }

        return $products;
    }

    /**
     * @param $product
     * @return array
     */
    public function getProductLayer($product)
    {
        $viewItem = [
            'item_id' => $product->getSku(),
            'item_name' => $product->getName(),
            'p_id' => (int) $product->getId(),
            'currency' => $this->getStoreCurrencyCode()
            //'brand' => 'Google',
            //'variant' => 'Gray',
        ];

        if ($category = $this->getProductCategoryName()) {
            $viewItem['item_category'] = $category;
        }

        $this->_eeHelper->addCategoryElements($product, $viewItem);

        if ($price = $this->formatPrice($product->getFinalPrice())) {
            $viewItem['price'] = $price;
        }

        return $viewItem;
    }

    /**
     * Get category name from breadcrumb
     *
     * @return string
     */
    protected function getProductCategoryName()
    {
        $categoryName = '';

        try {
            $categoryArray = $this->getBreadCrumbPath();

            if (count($categoryArray) > 1) {
                end($categoryArray);
                $categoryName = prev($categoryArray);
            }
        } catch (Exception $e) {
            return $categoryName;
        }

        return $categoryName;
    }

    /**
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function getRelatedProduct()
    {
        $this->setBlockName($this->_eeHelper->getRelatedBlockName());
        $this->setItemListName($this->_eeHelper->getRelatedItemListName());
        return $this->getProductImpressions($this->_getProducts(true));
    }

    /**
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function getUpsellProduct()
    {
        $this->setBlockName($this->_eeHelper->getUpsellBlockName());
        $this->setItemListName($this->_eeHelper->getUpsellItemListName());
        return $this->getProductImpressions($this->_getProducts(true));
    }
}

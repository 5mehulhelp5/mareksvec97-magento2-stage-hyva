<?php
/**
 * Copyright © MagePal LLC. All rights reserved.
 * See COPYING.txt for license details.
 * https://www.magepal.com | support@magepal.com
 */

namespace MagePal\GoogleAnalytics4\Block\Data;

use Magento\Catalog\Helper\Data as CatalogData;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use MagePal\GoogleAnalytics4\Block\CatalogLayer;
use MagePal\GoogleTagManager\DataLayer\ProductData\ProductImpressionProvider;
use MagePal\GoogleAnalytics4\Helper\Data;
use MagePal\GoogleAnalytics4\Helper\Esp;
use MagePal\GoogleTagManager\Helper\Data as GtmHelper;
use MagePal\GoogleTagManager\Model\DataLayerEvent;

/**
 * Enhanced Success Page Extension
 */
class EnhancedSuccessPage extends CatalogLayer
{
    /** @var Esp */
    protected $espHelper;

    /**
     * CatalogLayer constructor.
     * @param Context $context
     * @param Resolver $layerResolver
     * @param Registry $registry
     * @param CatalogData $catalogHelper
     * @param GtmHelper $gtmHelper
     * @param Data $eeHelper
     * @param ProductImpressionProvider $productImpressionProvider
     * @param Esp $espHelper
     * @param array $data
     * @throws NoSuchEntityException
     */
    public function __construct(
        Context $context,
        Resolver $layerResolver,
        Registry $registry,
        CatalogData $catalogHelper,
        GtmHelper $gtmHelper,
        Data $eeHelper,
        ProductImpressionProvider $productImpressionProvider,
        Esp $espHelper,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $layerResolver,
            $registry,
            $catalogHelper,
            $gtmHelper,
            $eeHelper,
            $productImpressionProvider,
            $data
        );
        $this->espHelper = $espHelper;
    }

    /**
     * Add category data to datalayer
     *
     * @return $this
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function _dataLayer()
    {
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
                $this->espHelper->getRelatedItemListName(),
                $this->espHelper->getRelatedClassName(),
                $this->espHelper->getRelatedContainerClass()
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

            $this->addCustomDataLayer($impressionsListData, 21);

            $this->setImpressionList(
                $this->espHelper->getUpsellItemListName(),
                $this->espHelper->getUpsellClassName(),
                $this->espHelper->getUpsellContainerClass()
            );
        }

        $crossSellProduct = $this->getCrossSellProduct();
        if (!empty($crossSellProduct)) {
            $impressionsListData = [
                'event' => DataLayerEvent::GA4_VIEW_ITEM_LIST,
                'ecommerce' => [
                    'items' => $crossSellProduct,
                    'currency' => $this->getStoreCurrencyCode(),
                    'item_list_name' => $this->getItemListName(),
                    'item_list_id'=> $this->getItemListId()
                ],
                '_clear' => true
            ];

            $this->addCustomDataLayer($impressionsListData, 21);

            $this->setImpressionList(
                $this->espHelper->getCrosssellItemListName(),
                $this->espHelper->getCrosssellClassName(),
                $this->espHelper->getCrosssellContainerClass()
            );
        }

        $recentlyViewedProduct = $this->geRecentlyViewProduct();
        if (!empty($recentlyViewedProduct)) {
            $impressionsListData = [
                'event' => DataLayerEvent::GA4_VIEW_ITEM_LIST,
                'ecommerce' => [
                    'items' => $recentlyViewedProduct,
                    'currency' => $this->getStoreCurrencyCode(),
                    'item_list_name' => $this->getItemListName(),
                    'item_list_id'=> $this->getItemListId()
                ],
                '_clear' => true
            ];

            $this->addCustomDataLayer($impressionsListData, 21);

            $this->setImpressionList(
                $this->espHelper->getRecentViewedItemListName(),
                $this->espHelper->getRecentViewedClassName(),
                $this->espHelper->getRecentViewedContainerClass()
            );
        }

        return $this;
    }

    /**
     * @return bool
     */
    protected function isRelatedOrCrosssell()
    {
        return true;
    }

    /**
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function getRelatedProduct()
    {
        $this->setBlockName('product.related');
        $this->setItemListName($this->espHelper->getRelatedItemListName());
        return $this->getProductImpressions($this->_getProductCollection(true));
    }

    /**
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function getUpsellProduct()
    {
        $this->setBlockName('product.upsell');
        $this->setItemListName($this->espHelper->getUpsellItemListName());
        return $this->getProductImpressions($this->_getProductCollection(true));
    }

    /**
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function getCrossSellProduct()
    {
        $this->setBlockName('product.crosssell');
        $this->setItemListName($this->espHelper->getCrosssellItemListName());
        return $this->getProductImpressions($this->_getProductCollection(true));
    }

    /**
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function geRecentlyViewProduct()
    {
        $this->setBlockName('product.recently.view');
        $this->setItemListName($this->espHelper->getRecentViewedItemListName());
        return $this->getProductImpressions($this->_getProductCollection(true));
    }
}

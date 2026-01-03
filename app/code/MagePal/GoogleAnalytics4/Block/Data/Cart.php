<?php
/**
 * Copyright © MagePal LLC. All rights reserved.
 * See COPYING.txt for license details.
 * https://www.magepal.com | support@magepal.com
 */

namespace MagePal\GoogleAnalytics4\Block\Data;

use Magento\Catalog\Helper\Data as CatalogData;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Magento\Quote\Model\Quote;
use MagePal\GoogleAnalytics4\Block\CatalogLayer;
use MagePal\GoogleAnalytics4\Helper\DataLayerItem;
use MagePal\GoogleTagManager\DataLayer\ProductData\ProductImpressionProvider;
use MagePal\GoogleAnalytics4\Helper\Data;
use MagePal\GoogleAnalytics4\Model\Session as EnhancedEcommerceSession;

use MagePal\GoogleTagManager\DataLayer\QuoteData\QuoteItemProvider;
use MagePal\GoogleTagManager\Helper\Data as GtmHelper;
use MagePal\GoogleTagManager\Model\DataLayerEvent;

/**
 * @method setBlockName($name);
 * @method setItemListName($type);
 */
class Cart extends CatalogLayer
{
    /**
     * @var Session
     */
    private $checkoutSession;
    /**
     * @var DataLayerItem
     */
    private $dataLayerItemHelper;
    /**
     * @var QuoteItemProvider
     */
    private $quoteItemProvider;

    /**
     * Cart constructor.
     * @param Context $context
     * @param Resolver $layerResolver
     * @param Registry $registry
     * @param CatalogData $catalogHelper
     * @param EnhancedEcommerceSession $enhancedEcommerceSession
     * @param Data $eeHelper
     * @param GtmHelper $gtmHelper
     * @param ProductImpressionProvider $productImpressionProvider
     * @param Session $checkoutSession
     * @param DataLayerItem $dataLayerItemHelper
     * @param QuoteItemProvider $quoteItemProvider
     * @param array $data
     * @throws NoSuchEntityException
     */
    public function __construct(
        Context $context,
        Resolver $layerResolver,
        Registry $registry,
        CatalogData $catalogHelper,
        EnhancedEcommerceSession $enhancedEcommerceSession,
        Data $eeHelper,
        GtmHelper $gtmHelper,
        ProductImpressionProvider $productImpressionProvider,
        Session $checkoutSession,
        DataLayerItem  $dataLayerItemHelper,
        QuoteItemProvider $quoteItemProvider,
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

        $this->enhancedEcommerceSession = $enhancedEcommerceSession;
        $this->checkoutSession = $checkoutSession;
        $this->dataLayerItemHelper = $dataLayerItemHelper;
        $this->quoteItemProvider = $quoteItemProvider;
    }

    /**
     * @var EnhancedEcommerceSession
     */
    protected $enhancedEcommerceSession;

    /**
     * Add category data to datalayer
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function _dataLayer()
    {
        $cartData = [
            'event' => DataLayerEvent::GA4_VIEW_CART,
            'ecommerce' => [
                'currency' => $this->getStoreCurrencyCode(),
                'items' => $this->getCart(),
                'value' => $this->getCartSubTotal()
            ],
            '_clear' => true
        ];

        $this->addCustomDataLayerByEvent(DataLayerEvent::GA4_VIEW_CART, $cartData);

        $list = $this->getCrossSellProduct();
        if ($list && count($list)) {
            $this->setImpressionList(
                $this->_eeHelper->getCrosssellItemListName(),
                $this->_eeHelper->getCrosssellClassName(),
                $this->_eeHelper->getCrosssellContainerClass()
            );

            $impressionsData = [
                'event' => DataLayerEvent::GA4_VIEW_ITEM_LIST,
                'ecommerce' => [
                    'currency' => $this->getStoreCurrencyCode(),
                    'items' => $list,
                    'item_list_name' => $this->getItemListName(),
                    'item_list_id'=> $this->getItemListId()
                ],
                '_clear' => true
            ];

            $this->addCustomDataLayer($impressionsData, 22);
        }

        $pushObject = $this->enhancedEcommerceSession->getProductDataObjectArray();

        if (!empty($pushObject) && is_array($pushObject)) {
            foreach ($pushObject as $object) {
                if (array_key_exists('event', $object)) {
                    if ($object['event'] == DataLayerEvent::GA4_ADD_TO_CART_EVENT) {
                        $action = [
                            'cart' => [
                                'add' => $object['ecommerce']['items']
                            ],
                            '_clear' => true
                        ];
                        $this->addCustomDataLayer($action);
                    } elseif ($object['event'] == DataLayerEvent::GA4_REMOVE_FROM_CART_EVENT) {
                        $action = [
                            'cart' => [
                                'remove' => $object['ecommerce']['items']
                            ],
                            '_clear' => true
                        ];
                        $this->addCustomDataLayer($action);
                    }
                }

                $this->addCustomDataLayer($object);
            }
        }

        return $this;
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    protected function getCrossSellProduct()
    {
        $this->setBlockName($this->_eeHelper->getCrosssellBlockName());
        $this->setItemListName($this->_eeHelper->getCrosssellItemListName());
        return $this->getProductImpressions($this->_getProducts(true));
    }

    /**
     * Get active quote
     *
     * @return Quote
     */
    public function getQuote()
    {
        return $this->checkoutSession->getQuote();
    }

    /**
     * @return float
     */
    public function getCartSubTotal()
    {
        return $this->getQuote() ? $this->getQuote()->getGrandTotal() : 0.00;
    }

    public function getCart()
    {
        $quote = $this->getQuote();

        $items = [];

        if ($quote && $quote->getItemsCount()) {
            // set items
            foreach ($quote->getAllVisibleItems() as $item) {
                $object = $this->dataLayerItemHelper->getProductObject(
                    $item,
                    $item->getQty()
                );

                $items[] = $this->quoteItemProvider
                    ->setItemData($object)
                    ->setItem($item)
                    ->setActionType(QuoteItemProvider::ACTION_VIEW_CART)
                    ->setListType(QuoteItemProvider::LIST_TYPE_GOOGLE)
                    ->getData();
            }
        }

        return $items;
    }
}

<?php
/**
 * Copyright © MagePal LLC. All rights reserved.
 * See COPYING.txt for license details.
 * https://www.magepal.com | support@magepal.com
 */

namespace MagePal\GoogleAnalytics4\Plugin\Model\Order;

use Magento\Sales\Model\ResourceModel\Order\CollectionFactoryInterface;
use MagePal\GoogleTagManager\DataLayer\OrderData\OrderItemAbstract;
use MagePal\GoogleTagManager\DataLayer\OrderData\OrderItemProvider;
use MagePal\GoogleTagManager\DataLayer\OrderData\OrderProvider;
use MagePal\GoogleAnalytics4\Helper\Data as Ga4Helper;
use MagePal\GoogleTagManager\Helper\DataLayerItem;
use MagePal\GoogleTagManager\Model\DataLayerEvent;
use MagePal\GoogleTagManager\Model\Order;

class OrderDataLayerPlugin
{
    /**
     * @var ga4Helper
     */
    protected $ga4Helper;

    /**
     * @var CollectionFactoryInterface
     */
    protected $_salesOrderCollection;

    /**
     * @var null
     */
    protected $_orderCollection = null;

    /**
     * @var OrderProvider
     */
    protected $orderProvider;

    /**
     * @var OrderItemProvider
     */
    protected $orderItemProvider;

    protected $dataLayerItemHelper;

    public function __construct(
        CollectionFactoryInterface $salesOrderCollection,
        Ga4Helper $ga4Helper,
        OrderProvider $orderProvider,
        OrderItemProvider $orderItemProvider,
        DataLayerItem  $dataLayerItemHelper
    ) {
        $this->ga4Helper = $ga4Helper;
        $this->_salesOrderCollection = $salesOrderCollection;
        $this->orderProvider = $orderProvider;
        $this->orderItemProvider = $orderItemProvider;
        $this->dataLayerItemHelper = $dataLayerItemHelper;
    }

    /**
     * @param Order $subject
     * @param $result
     * @param $order
     */
    public function afterGetTransactionDetail(Order $subject, $result, $order)
    {

        if (!$this->ga4Helper->isUaPurchaseTrackingEnabled()) {
            $defaultGaFields = [
                'transactionId',
                'transactionAffiliation',
                'transactionTotal',
                'transactionSubTotal',
                'transactionShipping',
                'transactionTax',
                'transactionCouponCode',
                'transactionDiscount',
                'transactionProducts'
            ];

            foreach ($defaultGaFields as $key) {
                if (array_key_exists($key, $result)) {
                    unset($result[$key]);
                }
            }
        }

        $transaction = [
            'ecommerce' => $this->getOrderDetail($order),
            '_clear' => true
        ];

        $data = array_merge_recursive($result, $transaction);
        $data['event'] = DataLayerEvent::PURCHASE_EVENT;

        return $data;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return array
     */
    public function getOrderDetail($order)
    {
        $purchase = [
            'purchase' => [
                'transaction_id' => $order->getIncrementId(),
                'affiliation' => $this->escapeReturn($order->getStoreName()),
                'value' => $this->ga4Helper->formatPrice($order->getBaseGrandTotal()),
                'tax' => $this->ga4Helper->formatPrice($order->getTaxAmount()),
                'shipping' => $this->ga4Helper->formatPrice($order->getBaseShippingAmount()),
                'currency' => $order->getStoreCurrencyCode(),
                'items' => $this->getItems($order)
            ],
            'currency' => $order->getStoreCurrencyCode()
        ];

        if ($order->getCouponCode()) {
            $purchase['purchase']['coupon'] = $order->getCouponCode();
        }
        return $purchase;
    }

    /**
     * @param $order
     * @return array
     */
    public function getItems($order)
    {
        $products = [];
        foreach ($order->getAllVisibleItems() as $item) {
            $viewItem = [
                'item_id' => $item->getSku(),
                'parent_sku' => $item->getProduct() ? $item->getProduct()->getData('sku') : $item->getSku(),
                'item_name' => $item->getName(),
                'price' => $this->ga4Helper->formatPrice($item->getBasePrice()),
                'quantity' => $item->getQtyOrdered() * 1
            ];

            if ($variant = $this->dataLayerItemHelper->getItemVariant($item)) {
                $viewItem['item_variant'] = $variant;
            }

            $this->dataLayerItemHelper->addCategoryElements($item->getProduct(), $viewItem);

            $products[] = $this->orderItemProvider
                ->setItem($item)
                ->setItemData($viewItem)
                ->setListType(OrderItemAbstract::LIST_TYPE_GOOGLE)
                ->getData();
        }

        return $products;
    }

    /**
     * @param $data
     * @return string
     */
    public function escapeReturn($data)
    {
        return trim(str_replace(["\r\n", "\r", "\n"], ' ', $data));
    }
}

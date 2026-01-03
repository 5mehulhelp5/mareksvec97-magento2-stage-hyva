<?php
/**
 * Copyright © MagePal LLC. All rights reserved.
 * See COPYING.txt for license details.
 * https://www.magepal.com | support@magepal.com
 */

namespace MagePal\GoogleAnalytics4\Observer\Adminhtml;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item;
use MagePal\GoogleAnalytics4\Helper\Data;
use MagePal\GoogleAnalytics4\Model\Session\Admin\CancelOrder;

/**
 * Google Analytics module observer
 *
 */
class OrderCancelAfterObserver extends OrderItem implements ObserverInterface
{
    /**
     * @var CancelOrder
     */
    protected $cancelOrderSession;

    /**
     * CreditmemoPlugin constructor.
     * @param Data $eeHelper
     * @param CancelOrder $cancelOrderSession
     */
    public function __construct(
        Data $eeHelper,
        CancelOrder $cancelOrderSession
    ) {
        parent::__construct($eeHelper);
        $this->cancelOrderSession = $cancelOrderSession;
    }

    /**
     * Add order information into GA block to render on checkout success pages
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        /* @var Order $order */
        $order = $observer->getOrder();

        if ($order && $this->helper->isRefundEnabled($order->getStoreId())) {
            $this->cancelOrderSession->setOrderId($order->getId());
            $this->cancelOrderSession->setIncrementId($order->getIncrementId());
            $this->cancelOrderSession->setBaseCurrencyCode($order->getBaseCurrencyCode());
            $this->cancelOrderSession->setStoreId($order->getStoreId());
            $this->cancelOrderSession->setAmount($order->getBaseGrandTotal());

            $products = [];
            /** @var Item $item */
            foreach ($order->getAllVisibleItems() as $item) {
                $products[] = $this->getItem($item);
            }

            $this->cancelOrderSession->setProducts($products);
        }
    }
}

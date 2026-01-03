<?php
/**
 * Copyright © MagePal LLC. All rights reserved.
 * See COPYING.txt for license details.
 * https://www.magepal.com | support@magepal.com
 */

namespace MagePal\GoogleAnalytics4\Model;

use Magento\Framework\Session\SessionManager;
use MagePal\GoogleTagManager\Model\DataLayerEvent;

/**
 * @method setData($name, $value)
 */
class Session extends SessionManager
{
    /**
     * @param $items
     * @return Session
     */
    public function setItemAddToCart($items)
    {
        $data = $this->getData('updated_qty_items');

        if (!empty($items) && is_array($data)) {
            $items = array_merge($data, $items);
        }

        $this->setData('updated_qty_items', $items);

        return $this;
    }

    /**
     * @param bool $clear
     * @return mixed
     */
    public function getItemAddToCart($clear = false)
    {
        return $this->getData('updated_qty_items', $clear);
    }

    /**
     * @param $items
     * @return $this
     */
    public function setItemRemovedFromCart(array $items)
    {
        $data = $this->getData('deleted_qty_items');

        if (!empty($items) && is_array($data)) {
            $items = array_merge($data, $items);
        }

        $this->setData('deleted_qty_items', $items);

        return $this;
    }

    /**
     * @param bool $clear
     * @return mixed
     */
    public function getItemRemovedFromCart($clear = false)
    {
        return $this->getData('deleted_qty_items', $clear);
    }

    /**
     * @param $item
     * @param $event
     * @return Session
     */
    public function setGenericEvent($item, $event)
    {
        $data = $this->getData('generic_items');
        $items = [];

        if (!empty($item)) {
            $items[] = [
                'event' => $event,
                'productErrors' => [
                    'product' => $item
                ],
                '_clear' => true
            ];

            if (!empty($items) && is_array($data)) {
                $items = array_merge($data, $items);
            }

            $this->setData('generic_items', $items);
        }

        return $this;
    }

    /**
     * @param bool $clear
     * @return mixed
     */
    public function getGenericEvent($clear = false)
    {
        return (array) $this->getData('generic_items', $clear);
    }

    /**
     * @return array
     */
    public function getProductDataObjectArray()
    {
        $itemAdded = $this->getItemAddToCart(true);
        $itemRemoved = $this->getItemRemovedFromCart(true);
        $genericEvents = $this->getGenericEvent(true);

        $result = [];

        if (!empty($itemAdded) && is_array($itemAdded)) {
            $result[] =  [
                'event' => DataLayerEvent::GA4_ADD_TO_CART_EVENT,
                'ecommerce' => [
                    'items' => $itemAdded,
                    'value' => $this->getTotal($itemAdded)
                ],
                '_clear' => true
            ];
        }

        if (!empty($itemRemoved) && is_array($itemRemoved)) {
            $result[] =  [
                'event' => DataLayerEvent::GA4_REMOVE_FROM_CART_EVENT,
                'ecommerce' => [
                    'items' => $itemRemoved,
                    'value' => $this->getTotal($itemRemoved)
                ],
                '_clear' => true
            ];
        }

        if (!empty($genericEvents)) {
            foreach ($genericEvents as $event) {
                $result[] = $event;
            }
        }

        return $result;
    }

    public function getTotal($items)
    {
        $total = 0;

        foreach ($items as $item) {
            $qty = isset($item['quantity']) ? $item['quantity'] : 0;
            $total += $qty * isset($item['price']) ? $item['price'] : 0;
            $total -= $qty * isset($item['discount']) ? $item['discount'] : 0;
        }

        return $total * 1;
    }
}

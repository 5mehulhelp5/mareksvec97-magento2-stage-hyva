<?php
/**
 * Copyright © MagePal LLC. All rights reserved.
 * See COPYING.txt for license details.
 * https://www.magepal.com | support@magepal.com
 */

namespace MagePal\GoogleAnalytics4\Observer\Adminhtml;

use MagePal\GoogleAnalytics4\Helper\Data;

class OrderItem
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * CreditmemoPlugin constructor.
     * @param Data $eeHelper
     */
    public function __construct(
        Data $eeHelper
    ) {
        $this->helper = $eeHelper;
    }

    /**
     * @param $item
     * @return array
     */
    protected function getItem($item)
    {
        $viewItem = [
            'item_name' => $item->getName(),
            'item_id' => $item->getSku(),
            'price' => $item->getPrice(),
            'quantity' => $item->getQty() * 1,
        ];

        $this->helper->addCategoryElements($item->getProduct(), $viewItem);

        return $viewItem;
    }
}

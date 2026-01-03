<?php

namespace N1site\Checkout\Plugin;

use Magento\Quote\Model\Quote\Item;

class DefaultItem {
	
    public function aroundGetItemData($subject, \Closure $proceed, Item $item) {
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$stockRegistry = $objectManager->get('Magento\CatalogInventory\Api\StockRegistryInterface');
		
        $data = $proceed($item);
        $product = $item->getProduct();
		
		$stockItem = $stockRegistry->getStockItemBySku($product->getSku());

        $atts = [
            "qty_increments" => round($stockItem->getData('qty_increments'), 2),
            "min_sale_qty" => round($stockItem->getData('min_sale_qty'), 2),
            // "product_qty" => $product->getQty()
        ];

        return array_merge($data, $atts);
    }
}
<?php 
namespace MetaloPro\GateCategoryControl\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Registry;

class RemoveAddToCartIfSearchOnly implements ObserverInterface
{
    protected $registry;

    public function __construct(
        Registry $registry
    ) {
        $this->registry = $registry;
    }

    public function execute(Observer $observer)
    {
        $product = $this->registry->registry('current_product');

        // 3 = VISIBLE_IN_SEARCH
        if ($product && (int)$product->getVisibility() === \Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_SEARCH) {
            $layout = $observer->getLayout();
            $layout->unsetElement('product.info.addtocart');
        }
    }
}

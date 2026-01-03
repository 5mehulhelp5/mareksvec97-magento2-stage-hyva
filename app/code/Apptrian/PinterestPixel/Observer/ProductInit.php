<?php
/**
 * @category  Apptrian
 * @package   Apptrian_PinterestPixel
 * @author    Apptrian
 * @copyright Copyright (c) Apptrian (http://www.apptrian.com)
 * @license   http://www.apptrian.com/license Proprietary Software License EULA
 */
 
namespace Apptrian\PinterestPixel\Observer;

class ProductInit implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Apptrian\PinterestPixel\Service\CurrentProduct
     */
    public $currentProduct;
    
    /**
     * Constructor.
     *
     * @param \Apptrian\PinterestPixel\Service\CurrentProduct $currentProduct
     */
    public function __construct(
        \Apptrian\PinterestPixel\Service\CurrentProduct $currentProduct
    ) {
        $this->currentProduct = $currentProduct;
    }
    
    /**
     * Execute method.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return \Apptrian\PinterestPixel\Observer\ProductInit
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product   = $observer->getEvent()->getProduct();
        $productId = 0;
        
        if ($product) {
            $productId = $product->getId();
            
            $this->currentProduct->setProductId($productId);
            $this->currentProduct->setProduct($product);
        }
        
        return $this;
    }
}

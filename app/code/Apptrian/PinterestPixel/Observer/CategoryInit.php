<?php
/**
 * @category  Apptrian
 * @package   Apptrian_PinterestPixel
 * @author    Apptrian
 * @copyright Copyright (c) Apptrian (http://www.apptrian.com)
 * @license   http://www.apptrian.com/license Proprietary Software License EULA
 */
 
namespace Apptrian\PinterestPixel\Observer;

class CategoryInit implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Apptrian\PinterestPixel\Service\CurrentCategory
     */
    public $currentCategory;
    
    /**
     * Constructor.
     *
     * @param \Apptrian\PinterestPixel\Service\CurrentCategory $currentCategory
     */
    public function __construct(
        \Apptrian\PinterestPixel\Service\CurrentCategory $currentCategory
    ) {
        $this->currentCategory = $currentCategory;
    }
    
    /**
     * Execute method.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return \Apptrian\PinterestPixel\Observer\CustomerInit
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $category   = $observer->getEvent()->getCategory();
        $categoryId = 0;
        
        if ($category) {
            $categoryId = $category->getId();
            
            $this->currentCategory->setCategoryId($categoryId);
            $this->currentCategory->setCategory($category);
        }
        
        return $this;
    }
}

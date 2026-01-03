<?php
namespace MetaloPro\GateCategoryControl\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Registry;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Page\Config as PageConfig;
use MetaloPro\GateCategoryControl\Helper\Category as CategoryHelper;

class RemoveSubcategoriesBlock implements ObserverInterface
{
    protected $categoryHelper;
    protected $pageConfig;

    public function __construct(CategoryHelper $categoryHelper, PageConfig $pageConfig)
    {
        $this->categoryHelper = $categoryHelper;
        $this->pageConfig = $pageConfig;
    }

    public function execute(Observer $observer)
    {
        $layout = $observer->getEvent()->getLayout();

        if ($this->categoryHelper->shouldHideSubcategories()) {
            $layout->unsetElement('apptrian.subcategories.category.page.before');
            $layout->unsetElement('apptrian.subcategories.category.page.after');
            $layout->unsetElement('category-custom-production');
            $layout->unsetElement('product-custom-production');
            $this->pageConfig->addBodyClass('brany-ploty-kategoria');
        }
    }
}


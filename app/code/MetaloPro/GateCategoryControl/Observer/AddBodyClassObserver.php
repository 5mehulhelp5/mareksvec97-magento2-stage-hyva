<?php 
namespace MetaloPro\GateCategoryControl\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Page\Config as PageConfig;
use MetaloPro\GateCategoryControl\Helper\Category as CategoryHelper;

class AddBodyClassObserver implements ObserverInterface
{
    protected $pageConfig;
    protected $categoryHelper;

    public function __construct(
        PageConfig $pageConfig,
        CategoryHelper $categoryHelper
    ) {
        $this->pageConfig = $pageConfig;
        $this->categoryHelper = $categoryHelper;
    }

    public function execute(Observer $observer)
    {
        error_log('[DEBUG] AddBodyClassObserver spusteny');

        if (!$this->categoryHelper->shouldRenderSubcategoryListing()) {
            error_log('[DEBUG] Pridavam class: hide-subcategories');
            $this->pageConfig->addBodyClass('hide-subcategories');
        }
    }
}

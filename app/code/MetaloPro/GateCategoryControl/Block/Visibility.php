<?php
namespace MetaloPro\GateCategoryControl\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;
use MetaloPro\GateCategoryControl\Helper\Image as ImageHelper;
use MetaloPro\GateCategoryControl\Helper\Category as CategoryHelper;
use Magento\Catalog\Api\CategoryRepositoryInterface;

class Visibility extends Template
{
    protected $categoryHelper;
    protected $imageHelper;
    protected $categoryRepository;

    public function __construct(
        Template\Context $context,
        CategoryHelper $categoryHelper,
        ImageHelper $imageHelper,
        CategoryRepositoryInterface $categoryRepository,
        array $data = []
    ) {
        $this->categoryHelper = $categoryHelper;
        $this->imageHelper = $imageHelper;
        $this->categoryRepository = $categoryRepository;
        parent::__construct($context, $data);
    }

    public function shouldHideSubcategories(): bool
    {
        return $this->categoryHelper->shouldHideSubcategories();
    }

    public function shouldRenderSubcategoryListing(): bool
    {
        return $this->categoryHelper->shouldRenderSubcategoryListing();
    }

    public function getCategoryImage($category): string
    {
        return $this->imageHelper->getImage($category);
    }

    public function getCategoryById($categoryId)
    {
        return $this->categoryRepository->get($categoryId);
    }
}

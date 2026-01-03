<?php
namespace MetaloPro\GateCategoryControl\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Registry;
use Magento\Catalog\Api\CategoryRepositoryInterface;

class Category extends AbstractHelper
{
    protected $registry;
    protected $categoryRepository;

    // ID kategórií, pre ktoré má byť blok skrytý
    protected $excludedCategoryIds = [83];

    public function __construct(
        Context $context,
        Registry $registry,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->registry = $registry;
        $this->categoryRepository = $categoryRepository;
        parent::__construct($context);
    }

    /**
     * Vracia true, ak sa má skryť CMS blok pre kategóriu alebo produkt.
     */
    public function shouldHideSubcategories(): bool
    {
        // Ak je na stránke kategórie
        $category = $this->registry->registry('current_category');
        if ($category && $this->isUnderExcludedCategory($category)) {
            return true;
        }

        // Ak je na stránke produktu
        $product = $this->registry->registry('current_product');
        if ($product) {
            // Pokus o získanie kategórie aj cez current_category
            $category = $this->registry->registry('current_category');
            if (!$category) {
                // Ak nie je, získať relevantnú z produktových kategórií
                $category = $this->resolveRelevantCategory($product);
            }

            if ($category && $this->isUnderExcludedCategory($category)) {
                return true;
            }
        }

        return false;
    }
    private function resolveRelevantCategory($product)
    {
        $categoryIds = $product->getCategoryIds();

        if (empty($categoryIds)) {
            return null;
        }

        $preferredCategory = null;
        $deepestCategory = null;
        $maxDepth = 0;

        foreach ($categoryIds as $categoryId) {
            try {
                $category = $this->categoryRepository->get($categoryId);
                $pathIds = explode('/', $category->getPath());
                $pathDepth = count($pathIds);

                // Preferuj kategórie, ktoré sú pod excluded ID
                foreach ($this->excludedCategoryIds as $excludedId) {
                    if (in_array((string)$excludedId, $pathIds, true)) {
                        if ($pathDepth > $maxDepth) {
                            $maxDepth = $pathDepth;
                            $preferredCategory = $category;
                        }
                        continue 2; // preskoč na ďalší categoryId
                    }
                }

                // Ak žiadna preferred, vezmi najhlbšiu
                if ($pathDepth > $maxDepth) {
                    $maxDepth = $pathDepth;
                    $deepestCategory = $category;
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return $preferredCategory ?? $deepestCategory;
    }

    /**
     * Vracia true, ak kategória alebo jej rodič je v excluded zozname, alebo je v strome pod excluded ID.
     */
    private function isUnderExcludedCategory($category): bool
    {
        $currentId = (int)$category->getId();
        $parentId = (int)$category->getParentId();
        $pathIds = explode('/', $category->getPath());

        foreach ($this->excludedCategoryIds as $excludedId) {
            if (
                $currentId === $excludedId ||
                $parentId === $excludedId ||
                in_array((string)$excludedId, $pathIds, true)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Vracia true, ak sa majú zobrazovať subkategórie (napr. iba pre root kategóriu).
     */
    public function shouldRenderSubcategoryListing(): bool
    {
        $category = $this->registry->registry('current_category');

        // Ak nie je current_category, skúsiť cez produkt
        if (!$category) {
            $product = $this->registry->registry('current_product');
            if ($product) {
                $category = $this->resolveRelevantCategory($product);
            }
        }

        if (!$category) {
            return false;
        }

        $rootCategoryId = 83;

        if ((int)$category->getId() === $rootCategoryId) {
            return true;
        }

        $pathIds = explode('/', $category->getPath());
        return in_array((string)$rootCategoryId, $pathIds, true);
    }
}

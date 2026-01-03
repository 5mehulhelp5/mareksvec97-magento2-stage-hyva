<?php


namespace MetaloPro\GateCategoryControl\Plugin\Catalog\Block\Product;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Registry;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class FilterByMaterial
{
    protected Registry $registry;
    protected CategoryFactory $categoryFactory;
    protected CollectionFactory $collectionFactory;

    public function __construct(
        Registry $registry,
        CategoryFactory $categoryFactory,
        CollectionFactory $collectionFactory
    ) {
        $this->registry = $registry;
        $this->categoryFactory = $categoryFactory;
        $this->collectionFactory = $collectionFactory;
    }

    public function afterGetLoadedProductCollection(ListProduct $subject, Collection $collection): Collection
    {
        try {
            $category = $this->registry->registry('current_category');
            if (!$category) return $collection;

            $categoryId = $category->getId();
            $categoryModel = $this->categoryFactory->create()->load($categoryId);
            $categoryName = $categoryModel->getName();
            $material = $categoryModel->getData('material_filter_value');

            if (!$material) return $collection;

            $customCollection = $this->collectionFactory->create();
            $customCollection->addAttributeToSelect('*')
                ->addAttributeToFilter('material', ['eq' => $material])
                ->addAttributeToFilter('type_id', 'simple')
                ->addAttributeToFilter('status', 1)
                ->addCategoriesFilter(['in' => [$categoryId]]);

            $subject->setCollection($customCollection);


            return $customCollection;

        } catch (\Exception $e) {
            // voliteľne: logovanie, ak by si chcel neskôr vrátiť LoggerInterface
        }

        return $collection;
    }
}

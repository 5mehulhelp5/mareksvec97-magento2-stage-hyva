<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Ui\Component\CategoryTemplate\Form\CategoryIds;

class Options implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Category tree cache id
     */
    const CATEGORY_TREE_ID = 'ECWHIM_SEOTEMPLATES_CATEGORY_TREE';

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    private $cacheManager;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * Options constructor.
     *
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Magento\Framework\App\CacheInterface $cacheManager
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Framework\App\CacheInterface $cacheManager,
        \Magento\Framework\Serialize\SerializerInterface $serializer
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->cacheManager              = $cacheManager;
        $this->serializer                = $serializer;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function toOptionArray()
    {
        return $this->getCategoryTree();
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getCategoryTree(): array
    {
        $cachedCategoryTree = $this->cacheManager->load($this->getCategoryTreeCacheId());

        if ($cachedCategoryTree) {
            return $this->serializer->unserialize($cachedCategoryTree);
        }

        $categoryTree = $this->retrieveCategoryTree();

        $this->cacheManager->save(
            $this->serializer->serialize($categoryTree),
            $this->getCategoryTreeCacheId(),
            [
                \Magento\Catalog\Model\Category::CACHE_TAG,
                \Magento\Framework\App\Cache\Type\Block::CACHE_TAG
            ]
        );

        return $categoryTree;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function retrieveCategoryTree(): array
    {
        $collection = $this->categoryCollectionFactory->create();
        $collection
            ->addAttributeToSelect(
                [
                    \Magento\Catalog\Model\Category::KEY_NAME,
                    \Magento\Catalog\Model\Category::KEY_IS_ACTIVE,
                    \Magento\Catalog\Model\Category::KEY_PARENT_ID
                ]
            )
            ->addAttributeToFilter('entity_id', ['neq' => \Magento\Catalog\Model\Category::TREE_ROOT_ID]);

        $categoryById = [\Magento\Catalog\Model\Category::TREE_ROOT_ID => ['optgroup' => []]];

        foreach ($collection as $category) {
            $categoryId = $category->getId();

            foreach ([$categoryId, $category->getParentId()] as $id) {
                if (empty($categoryById[$id])) {
                    $categoryById[$id] = ['value' => $id];
                }
            }

            $categoryById[$categoryId]['is_active']               = $category->getIsActive();
            $categoryById[$categoryId]['label']                   = $category->getName();
            $categoryById[$categoryId]['__disableTmpl']           = true;
            $categoryById[$category->getParentId()]['optgroup'][] = &$categoryById[$categoryId];
        }

        return $categoryById[\Magento\Catalog\Model\Category::TREE_ROOT_ID]['optgroup'];
    }

    /**
     * @return string
     */
    private function getCategoryTreeCacheId(): string
    {
        return self::CATEGORY_TREE_ID;
    }
}

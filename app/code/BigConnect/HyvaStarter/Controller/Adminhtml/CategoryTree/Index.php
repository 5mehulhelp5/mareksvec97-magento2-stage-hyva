<?php

declare(strict_types=1);

namespace BigConnect\HyvaStarter\Controller\Adminhtml\CategoryTree;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Store\Model\StoreManagerInterface;

class Index extends Action
{
    public const ADMIN_RESOURCE = 'BigConnect_HyvaStarter::category_tree';

    private JsonFactory $resultJsonFactory;
    private CategoryCollectionFactory $categoryCollectionFactory;
    private StoreManagerInterface $storeManager;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->storeManager = $storeManager;
    }

    public function execute()
    {
        $storeParam = (string) $this->getRequest()->getParam('store');
        if ($storeParam !== '') {
            $storeId = (int) $this->storeManager->getStore($storeParam)->getId();
        } else {
            $storeId = (int) $this->storeManager->getStore()->getId();
        }

        $collection = $this->categoryCollectionFactory->create();
        $collection->setStoreId($storeId);
        $collection->addAttributeToSelect(['name', 'is_active', 'path', 'level', 'parent_id']);
        $collection->addAttributeToFilter('is_active', 1);
        $collection->addAttributeToSort('path', 'ASC');

        $metadata = [];
        foreach ($collection as $category) {
            $metadata[(int) $category->getId()] = [
                'name' => (string) $category->getName(),
                'level' => (int) $category->getLevel(),
                'parent_id' => (int) $category->getParentId(),
                'path' => (string) $category->getPath(),
            ];
        }

        $nodes = [];
        foreach ($collection as $category) {
            $level = (int) $category->getLevel();
            if ($level <= 1) {
                continue;
            }

            $id = (int) $category->getId();
            $nodes[$id] = [
                'id' => $id,
                'text' => $this->buildBreadcrumb($category->getPath(), $metadata),
                'children' => [],
            ];
        }

        $tree = [];
        foreach ($nodes as $id => &$node) {
            $parentId = $metadata[$id]['parent_id'] ?? 0;
            if (isset($nodes[$parentId]) && ($metadata[$parentId]['level'] ?? 0) > 1) {
                $nodes[$parentId]['children'][] = &$node;
            } else {
                $tree[] = &$node;
            }
        }
        unset($node);

        return $this->resultJsonFactory->create()->setData($tree);
    }

    private function buildBreadcrumb(string $path, array $metadata): string
    {
        $parts = array_filter(explode('/', $path));
        $labels = [];
        foreach ($parts as $id) {
            $id = (int) $id;
            if (!isset($metadata[$id]) || ($metadata[$id]['level'] ?? 0) <= 1) {
                continue;
            }
            $labels[] = $metadata[$id]['name'];
        }

        return implode(' / ', $labels);
    }
}

<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model\ProductTemplate;

use Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterface;

class TemplateProductsResolver implements TemplateProductsResolverInterface
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Magento\CatalogRule\Model\ResourceModel\Product\ConditionsToCollectionApplier
     */
    protected $conditionsToCollectionApplier;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Iterator
     */
    protected $resourceIterator;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var array|null
     */
    protected $productIds;

    /**
     * TemplateProductsResolver constructor.
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\CatalogRule\Model\ResourceModel\Product\ConditionsToCollectionApplier $conditionsToCollectionApplier
     * @param \Magento\Framework\Model\ResourceModel\Iterator $resourceIterator
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\CatalogRule\Model\ResourceModel\Product\ConditionsToCollectionApplier $conditionsToCollectionApplier,
        \Magento\Framework\Model\ResourceModel\Iterator $resourceIterator,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        $this->productCollectionFactory      = $productCollectionFactory;
        $this->resource                      = $resource;
        $this->conditionsToCollectionApplier = $conditionsToCollectionApplier;
        $this->resourceIterator              = $resourceIterator;
        $this->productFactory                = $productFactory;
    }

    /**
     * @param ProductTemplateInterface|\Ecwhim\SeoTemplates\Model\ProductTemplate $template
     * @param array|null $productIds
     * @return array
     * @throws \Magento\Framework\Exception\InputException
     */
    public function getMatchingProductIds(ProductTemplateInterface $template, array $productIds = null): array
    {
        $this->productIds = [];

        if (empty($template->getStoreIds())) {
            return $this->productIds;
        }

        $groupedStoreIds = $this->getStoreIdsGroupedByWebsiteIds($template);

        if (empty($groupedStoreIds)) {
            return $this->productIds;
        }

        $storeIds = [];

        foreach ($groupedStoreIds as $websiteStoreIds) {
            $storeIds = array_merge($storeIds, $websiteStoreIds);
        }

        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection */
        $productCollection = $this->productCollectionFactory->create();

        if (!empty($productIds)) {
            $productCollection->addIdFilter($productIds);
        }

        if (!in_array(\Magento\Store\Model\Store::DEFAULT_STORE_ID, $template->getStoreIds())) {
            $productCollection->addWebsiteFilter(array_keys($groupedStoreIds));
        }

        $template->setCollectedAttributes([]);
        $template->getConditions()->collectValidatedAttributes($productCollection);

        if ($this->canPreMapProducts($template)) {
            $productCollection = $this->conditionsToCollectionApplier
                ->applyConditionsToCollection($template->getConditions(), $productCollection);
        }

        $this->resourceIterator->walk(
            $productCollection->getSelect(),
            [[$this, 'callbackValidateProduct']],
            [
                'storeIds' => $storeIds,
                'template' => $template,
                'product'  => $this->productFactory->create()
            ]
        );

        return $this->productIds;
    }

    /**
     * Callback function for product matching
     *
     * @param array $args
     * @return void
     */
    public function callbackValidateProduct(array $args): void
    {
        $results  = [];
        $storeIds = $args['storeIds'];
        $template = $args['template'];
        $product  = clone $args['product'];

        $product->setData($args['row']);

        foreach ($storeIds as $storeId) {
            $product->setStoreId($storeId);

            $results[$storeId] = $template->getConditions()->validate($product);
        }

        $this->productIds[$product->getId()] = $results;
    }

    /**
     * @param ProductTemplateInterface $template
     * @return array
     */
    protected function getStoreIdsGroupedByWebsiteIds(ProductTemplateInterface $template): array
    {
        if ($template->getScope() === \Ecwhim\SeoTemplates\Model\Source\Scope::SCOPE_GLOBAL) {
            return [[\Magento\Store\Model\Store::DEFAULT_STORE_ID]];
        }

        $result     = [];
        $connection = $this->resource->getConnection();
        $select     = $connection->select();
        $select->from($this->resource->getTableName('store'), ['store_id', 'website_id']);

        if (in_array(\Magento\Store\Model\Store::DEFAULT_STORE_ID, $template->getStoreIds())) {
            $select->where('store_id != ?', \Magento\Store\Model\Store::DEFAULT_STORE_ID);
        } else {
            $select->where('store_id IN (?)', $template->getStoreIds());
        }

        foreach ($connection->fetchAll($select) as $row) {
            $result[$row['website_id']][] = (int)$row['store_id'];
        }

        return $result;
    }

    /**
     * @param ProductTemplateInterface|\Ecwhim\SeoTemplates\Model\ProductTemplate $template
     * @return bool
     */
    protected function canPreMapProducts(ProductTemplateInterface $template): bool
    {
        $conditions = $template->getConditions();

        if (!$conditions || !$conditions->getConditions()) {
            return false;
        }

        return true;
    }
}

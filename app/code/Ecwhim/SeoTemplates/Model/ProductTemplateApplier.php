<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model;

use Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterface;
use Ecwhim\SeoTemplates\Model\Indexer\ProductTemplate\AbstractIndexer as AbstractProductTemplateIndexer;
use Ecwhim\SeoTemplates\Setup\Patch\Schema\AddProductTemplateProductTable;

class ProductTemplateApplier
{
    /**
     * @var \Magento\Framework\Indexer\ConfigInterface
     */
    private $indexerConfig;

    /**
     * @var \Magento\Framework\Indexer\IndexerInterfaceFactory
     */
    private $indexerFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    private $productResourceModel;

    /**
     * @var \Ecwhim\SeoTemplates\Model\TemplateStoreIdsResolver
     */
    private $templateStoreIdsResolver;

    /**
     * @var \Ecwhim\SeoTemplates\Model\TemplateFilterInterface
     */
    private $templateFilter;

    /**
     * @var \Ecwhim\SeoTemplates\Model\ResourceModel\ProductTemplate
     */
    private $templateResource;

    /**
     * @var int
     */
    private $batchSize;

    /**
     * ProductTemplateApplier constructor.
     *
     * @param \Magento\Framework\Indexer\ConfigInterface $indexerConfig
     * @param \Magento\Framework\Indexer\IndexerInterfaceFactory $indexerFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product $productResourceModel
     * @param \Ecwhim\SeoTemplates\Model\TemplateStoreIdsResolver $templateStoreIdsResolver
     * @param \Ecwhim\SeoTemplates\Model\TemplateFilterInterface $templateFilter
     * @param \Ecwhim\SeoTemplates\Model\ResourceModel\ProductTemplate $templateResource
     * @param int $batchSize
     */
    public function __construct(
        \Magento\Framework\Indexer\ConfigInterface $indexerConfig,
        \Magento\Framework\Indexer\IndexerInterfaceFactory $indexerFactory,
        \Magento\Catalog\Model\ResourceModel\Product $productResourceModel,
        \Ecwhim\SeoTemplates\Model\TemplateStoreIdsResolver $templateStoreIdsResolver,
        \Ecwhim\SeoTemplates\Model\TemplateFilterInterface $templateFilter,
        \Ecwhim\SeoTemplates\Model\ResourceModel\ProductTemplate $templateResource,
        int $batchSize = 1000
    ) {
        $this->indexerConfig            = $indexerConfig;
        $this->indexerFactory           = $indexerFactory;
        $this->productResourceModel     = $productResourceModel;
        $this->templateStoreIdsResolver = $templateStoreIdsResolver;
        $this->templateFilter           = $templateFilter;
        $this->templateResource         = $templateResource;
        $this->batchSize                = $batchSize;
    }

    /**
     * @param ProductTemplateInterface $template
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function apply(ProductTemplateInterface $template): bool
    {
        $this->validateTemplate($template);
        $this->validateIndexers();

        $type      = $template->getType();
        $attribute = $this->productResourceModel->getAttribute($type);

        if (!$attribute) {
            return false;
        }

        $attrTableName = $attribute->getBackend()->getTable();
        $updateFields  = $attribute->isStatic() ? [$attribute->getAttributeCode()] : ['value'];
        $storeIds      = $this->templateStoreIdsResolver->getStoreIds($template);
        $content       = $template->getContent();
        $connection    = $this->productResourceModel->getConnection();

        foreach ($storeIds as $storeId) {
            $storeId      = (int)$storeId;
            $select       = $this->prepareSelect($template->getTemplateId(), $storeId);
            $productCount = $this->getProductCount($select);
            $offset       = null;

            while ($productCount > 0) {
                $select->limit($this->batchSize, $offset);

                $productIds = $connection->fetchCol($select);
                $productIds = $this->filterProductIdsByStoreAvailability($productIds, $storeId);

                if (empty($productIds)) {
                    $offset       += $this->batchSize;
                    $productCount -= $this->batchSize;

                    continue;
                }

                $values = $this->templateFilter->massFilter($productIds, $content, $storeId, $type);

                if ($attribute->isStatic()) {
                    $data = $this->prepareDataToSaveForStaticAttr($productIds, $values, $attribute->getAttributeCode());
                } else {
                    $data = $this->prepareDataToSave($productIds, $values, (int)$attribute->getAttributeId(), $storeId);
                }

                if ($data) {
                    $connection->insertOnDuplicate($attrTableName, $data, $updateFields);
                }

                $offset       += $this->batchSize;
                $productCount -= $this->batchSize;
            }
        }

        $this->templateResource->afterApplicationProcess($template);

        return true;
    }

    /**
     * @param ProductTemplateInterface $template
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function validateTemplate(ProductTemplateInterface $template): bool
    {
        if (!$template->getIsActive()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Inactive template "%1" cannot be applied.', $template->getName())
            );
        }

        return true;
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function validateIndexers(): bool
    {
        foreach ($this->indexerConfig->getIndexers() as $indexerId => $indexerData) {
            if (isset($indexerData['shared_index'])
                && $indexerData['shared_index'] === AbstractProductTemplateIndexer::SHARED_INDEX
            ) {
                $indexer = $this->indexerFactory->create();
                $indexer->load($indexerId);

                if (!$indexer->isValid()) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('Indexer "%1" is not valid.', $indexer->getTitle())
                    );
                }
            }
        }

        return true;
    }

    /**
     * @param int $templateId
     * @param int $storeId
     * @return \Magento\Framework\DB\Select
     */
    private function prepareSelect(int $templateId, int $storeId): \Magento\Framework\DB\Select
    {
        $indexTableName = $this->productResourceModel->getTable(
            AddProductTemplateProductTable::TABLE_PRODUCT_TEMPLATE_PRODUCT
        );

        $select = $this->productResourceModel->getConnection()->select();
        $select
            ->from(['t1' => $indexTableName], AddProductTemplateProductTable::COLUMN_PRODUCT_ID)
            ->where('t1.' . ProductTemplateInterface::TEMPLATE_ID . ' = ?', $templateId)
            ->where('t1.' . AddProductTemplateProductTable::COLUMN_STORE_ID . ' = ?', $storeId)
            ->where('t1.' . ProductTemplateInterface::TEMPLATE_ID . ' = ?', $this->getSubSelect('t1'));

        return $select;
    }

    /**
     * @param string $indexTableAlias
     * @return \Magento\Framework\DB\Select
     */
    private function getSubSelect(string $indexTableAlias): \Magento\Framework\DB\Select
    {
        $tableName = $this->productResourceModel->getTable(
            AddProductTemplateProductTable::TABLE_PRODUCT_TEMPLATE_PRODUCT
        );
        $fields    = [
            AddProductTemplateProductTable::COLUMN_PRODUCT_ID,
            AddProductTemplateProductTable::COLUMN_STORE_ID,
            ProductTemplateInterface::TYPE,
            ProductTemplateInterface::SCOPE
        ];

        $select = $this->productResourceModel->getConnection()->select();
        $select->from(['t2' => $tableName], ProductTemplateInterface::TEMPLATE_ID);

        foreach ($fields as $field) {
            $select->where($indexTableAlias . '.' . $field . ' = ' . 't2.' . $field);
        }

        $select
            ->order('t2.' . ProductTemplateInterface::PRIORITY)
            ->order('t2.' . ProductTemplateInterface::TEMPLATE_ID)
            ->limit(1);

        return $select;
    }

    /**
     * @param \Magento\Framework\DB\Select $select
     * @return int
     */
    private function getProductCount(\Magento\Framework\DB\Select $select): int
    {
        $countSelect = clone $select;
        $countSelect->reset(\Magento\Framework\DB\Select::COLUMNS);
        $countSelect->columns('COUNT(*)');

        return (int)$this->productResourceModel->getConnection()->fetchOne($countSelect);
    }

    /**
     * @param array $productIds
     * @param int $storeId
     * @return array
     */
    private function filterProductIdsByStoreAvailability(array $productIds, int $storeId): array
    {
        if (empty($productIds) || $storeId === \Magento\Store\Model\Store::DEFAULT_STORE_ID) {
            return $productIds;
        }

        $connection  = $this->productResourceModel->getConnection();
        $select      = $connection->select();
        $idFieldName = $this->productResourceModel->getIdFieldName();

        $select
            ->from(['e' => $this->productResourceModel->getEntityTable()], [$idFieldName])
            ->joinInner(
                ['w' => $this->productResourceModel->getProductWebsiteTable()],
                'e.' . $idFieldName . ' = w.product_id',
                []
            )
            ->joinInner(
                ['s' => $this->productResourceModel->getTable('store')],
                'w.website_id = s.website_id',
                []
            )
            ->where('e.' . $idFieldName . ' IN(?)', $productIds)
            ->where('s.store_id = ?', $storeId);

        return $connection->fetchCol($select);
    }

    /**
     * @param array $productIds
     * @param array $values
     * @param string $attrCode
     * @return array
     */
    private function prepareDataToSaveForStaticAttr(array $productIds, array $values, string $attrCode): array
    {
        $data      = [];
        $linkField = $this->productResourceModel->getLinkField();

        if ($this->productResourceModel->getIdFieldName() == $linkField) {
            foreach ($values as $productId => $value) {
                $data[] = [$linkField => (int)$productId, $attrCode => $value];
            }
        } else {
            $linkFieldValues = $this->getLinkFieldValues($productIds);

            foreach ($values as $productId => $value) {
                if (!isset($linkFieldValues[$productId])) {
                    continue;
                }

                $data[] = [$linkField => (int)$linkFieldValues[$productId], $attrCode => $value];
            }
        }

        return $data;
    }

    /**
     * @param array $productIds
     * @param array $values
     * @param int $attributeId
     * @param int $storeId
     * @return array
     */
    private function prepareDataToSave(array $productIds, array $values, int $attributeId, int $storeId): array
    {
        $data      = [];
        $linkField = $this->productResourceModel->getLinkField();

        if ($this->productResourceModel->getIdFieldName() == $linkField) {
            foreach ($values as $productId => $value) {
                $data[] = [
                    'attribute_id' => $attributeId,
                    'store_id'     => $storeId,
                    $linkField     => (int)$productId,
                    'value'        => $value,
                ];
            }
        } else {
            $linkFieldValues = $this->getLinkFieldValues($productIds);

            foreach ($values as $productId => $value) {
                if (!isset($linkFieldValues[$productId])) {
                    continue;
                }

                $data[] = [
                    'attribute_id' => $attributeId,
                    'store_id'     => $storeId,
                    $linkField     => (int)$linkFieldValues[$productId],
                    'value'        => $value,
                ];
            }
        }

        return $data;
    }

    /**
     * @param array $entityIds
     * @return array
     */
    private function getLinkFieldValues(array $entityIds): array
    {
        $entityIdField = $this->productResourceModel->getIdFieldName();
        $linkField     = $this->productResourceModel->getLinkField();
        $tableName     = $this->productResourceModel->getTable('catalog_product_entity');
        $select        = $this->productResourceModel->getConnection()->select();
        $select
            ->from($tableName, [$entityIdField, $linkField])
            ->where($entityIdField . ' IN(?)', $entityIds);

        return $this->productResourceModel->getConnection()->fetchPairs($select);
    }
}

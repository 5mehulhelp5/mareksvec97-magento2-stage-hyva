<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model\TemplateFilter\DirectiveProcessor\AttrDirective;

class ProductAttrDirective extends AbstractAttrDirective
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    private $attributeCollectionFactory;

    /**
     * @var \Ecwhim\SeoTemplates\Model\ProductAttributeValueResolverFactory
     */
    private $attributeValueResolverFactory;

    /**
     * @var array|null
     */
    private $attributeCodes;

    /**
     * ProductAttrDirective constructor.
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory
     * @param \Ecwhim\SeoTemplates\Model\ProductAttributeValueResolverFactory $attributeValueResolverFactory
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory,
        \Ecwhim\SeoTemplates\Model\ProductAttributeValueResolverFactory $attributeValueResolverFactory
    ) {
        $this->productCollectionFactory      = $productCollectionFactory;
        $this->attributeCollectionFactory    = $attributeCollectionFactory;
        $this->attributeValueResolverFactory = $attributeValueResolverFactory;
    }

    /**
     * @param \Ecwhim\SeoTemplates\Model\TemplateFilter $filter
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function prepareData(\Ecwhim\SeoTemplates\Model\TemplateFilter $filter): void
    {
        $this->data     = [];
        $attributeCodes = $this->getAttributeCodes($filter->getTemplateContent());

        if ($attributeCodes) {
            $collection = $this->productCollectionFactory->create();
            $collection->setStoreId($filter->getStoreId());
            $collection->addIdFilter($filter->getEntityIds());
            $collection->addAttributeToSelect($attributeCodes, 'left');

            foreach ($collection->getData() as $entityData) {
                $entityId = $entityData[$collection->getIdFieldName()];

                $this->data[$entityId] = $entityData;
            }
        }
    }

    /**
     * @inheritDoc
     */
    protected function getAttributeValue(string $attributeCode, array $entityData, int $storeId): string
    {
        $valueResolver = $this->attributeValueResolverFactory->get($attributeCode);

        return $valueResolver->getAttributeValue($attributeCode, $entityData, $storeId);
    }

    /**
     * @inheritDoc
     */
    protected function validateAttributeCode(string $attributeCode): bool
    {
        $isValid = parent::validateAttributeCode($attributeCode);

        return $isValid && in_array($attributeCode, $this->getAvailableAttributeCodes());
    }

    /**
     * @inheritDoc
     */
    protected function getAttributeCodes(string $content): array
    {
        $codes = parent::getAttributeCodes($content);

        return array_intersect($codes, $this->getAvailableAttributeCodes());
    }

    /**
     * @return array
     */
    private function getAvailableAttributeCodes(): array
    {
        if (isset($this->attributeCodes)) {
            return $this->attributeCodes;
        }

        $collection = $this->attributeCollectionFactory->create();
        $select     = $collection->getSelect();
        $select->reset(\Magento\Framework\DB\Select::COLUMNS);
        $select->columns(\Magento\Eav\Api\Data\AttributeInterface::ATTRIBUTE_CODE);

        return $this->attributeCodes = $collection->getConnection()->fetchCol($select);
    }
}

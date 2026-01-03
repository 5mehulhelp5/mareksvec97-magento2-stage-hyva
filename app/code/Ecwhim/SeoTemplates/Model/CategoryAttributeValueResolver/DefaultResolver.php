<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model\CategoryAttributeValueResolver;

class DefaultResolver implements CategoryAttributeValueResolverInterface
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category
     */
    private $categoryResourceModel;

    /**
     * DefaultResolver constructor.
     *
     * @param \Magento\Catalog\Model\ResourceModel\Category $categoryResourceModel
     */
    public function __construct(\Magento\Catalog\Model\ResourceModel\Category $categoryResourceModel)
    {
        $this->categoryResourceModel = $categoryResourceModel;
    }

    /**
     * @param string $attributeCode
     * @param array $entityData
     * @param int $storeId
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAttributeValue(string $attributeCode, array $entityData, int $storeId): string
    {
        if (!isset($entityData[$attributeCode])) {
            return '';
        }

        $value     = (string)$entityData[$attributeCode];
        $attribute = $this->categoryResourceModel->getAttribute($attributeCode);

        if ($attribute && $attribute->usesSource()) {
            $attribute->setStoreId($storeId);

            $optionText = $attribute->getSource()->getOptionText($value);

            if ($optionText !== false && $optionText !== null) {
                return is_array($optionText) ? implode(', ', $optionText) : (string)$optionText;
            }
        }

        return $value;
    }
}

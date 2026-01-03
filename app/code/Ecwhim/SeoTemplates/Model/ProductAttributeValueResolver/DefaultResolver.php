<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model\ProductAttributeValueResolver;

class DefaultResolver implements ProductAttributeValueResolverInterface
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    private $productResourceModel;

    /**
     * DefaultResolver constructor.
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product $productResourceModel
     */
    public function __construct(\Magento\Catalog\Model\ResourceModel\Product $productResourceModel)
    {
        $this->productResourceModel = $productResourceModel;
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
        $attribute = $this->productResourceModel->getAttribute($attributeCode);

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

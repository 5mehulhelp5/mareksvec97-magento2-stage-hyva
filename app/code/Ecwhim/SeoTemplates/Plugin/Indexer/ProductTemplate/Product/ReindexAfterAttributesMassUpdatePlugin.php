<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Plugin\Indexer\ProductTemplate\Product;

use Ecwhim\SeoTemplates\Model\Indexer\ProductTemplate\Product\ProductTemplateProcessor;

class ReindexAfterAttributesMassUpdatePlugin
{
    /**
     * @var ProductTemplateProcessor
     */
    protected $productTemplateProcessor;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * ReindexAfterAttributesMassUpdatePlugin constructor.
     *
     * @param ProductTemplateProcessor $productTemplateProcessor
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(
        ProductTemplateProcessor $productTemplateProcessor,
        \Magento\Eav\Model\Config $eavConfig
    ) {
        $this->productTemplateProcessor = $productTemplateProcessor;
        $this->eavConfig                = $eavConfig;
    }

    /**
     * @param \Magento\Catalog\Model\Product\Action $subject
     * @param \Magento\Catalog\Model\Product\Action $result
     * @param array $productIds
     * @param array $attrData
     * @return \Magento\Catalog\Model\Product\Action
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterUpdateAttributes(
        \Magento\Catalog\Model\Product\Action $subject,
        \Magento\Catalog\Model\Product\Action $result,
        array $productIds,
        array $attrData
    ): \Magento\Catalog\Model\Product\Action {
        if (!$this->productTemplateProcessor->isIndexerScheduled()
            && $this->hasAttributesUsedForPromoRules($attrData)
        ) {
            $this->productTemplateProcessor->reindexList(array_unique($productIds));
        }

        return $result;
    }

    /**
     * @param array $attributesData
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function hasAttributesUsedForPromoRules(array $attributesData): bool
    {
        foreach ($attributesData as $code => $value) {
            $attribute = $this->eavConfig->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $code);

            if ($attribute->getIsUsedForPromoRules()) {
                return true;
            }
        }

        return false;
    }
}

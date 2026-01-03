<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model\ProductAttributeValueResolver;

class Price implements ProductAttributeValueResolverInterface
{
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * Price constructor.
     *
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     */
    public function __construct(\Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency)
    {
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * @inheritDoc
     */
    public function getAttributeValue(string $attributeCode, array $entityData, int $storeId): string
    {
        if (!isset($entityData[$attributeCode])) {
            return '';
        }

        $value = (float)$entityData[$attributeCode];

        if (empty($value)) {
            return '';
        }

        return $this->priceCurrency->convertAndFormat(
            $value,
            false,
            \Magento\Framework\Pricing\PriceCurrencyInterface::DEFAULT_PRECISION,
            $storeId
        );
    }
}

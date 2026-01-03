<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model\ProductAttributeValueResolver;

/**
 * @api
 */
interface ProductAttributeValueResolverInterface
{
    /**
     * @param string $attributeCode
     * @param array $entityData
     * @param int $storeId
     * @return string
     */
    public function getAttributeValue(string $attributeCode, array $entityData, int $storeId): string;
}

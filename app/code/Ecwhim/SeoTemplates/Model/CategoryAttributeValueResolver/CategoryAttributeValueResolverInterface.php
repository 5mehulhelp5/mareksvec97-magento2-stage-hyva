<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model\CategoryAttributeValueResolver;

/**
 * @api
 */
interface CategoryAttributeValueResolverInterface
{
    /**
     * @param string $attributeCode
     * @param array $entityData
     * @param int $storeId
     * @return string
     */
    public function getAttributeValue(string $attributeCode, array $entityData, int $storeId): string;
}

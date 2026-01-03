<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model\ProductTemplate;

/**
 * @api
 */
interface TemplateProductsResolverInterface
{
    /**
     * @param \Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterface $template
     * @param array|null $productIds
     * @return array
     */
    public function getMatchingProductIds(
        \Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterface $template,
        array $productIds = null
    ): array;
}

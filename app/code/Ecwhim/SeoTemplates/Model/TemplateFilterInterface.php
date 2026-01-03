<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model;

/**
 * @api
 */
interface TemplateFilterInterface
{
    /**
     * @param array $entityIds
     * @param string $content
     * @param int $storeId
     * @param string $type
     * @return string[]
     */
    public function massFilter(array $entityIds, string $content, int $storeId, string $type): array;
}

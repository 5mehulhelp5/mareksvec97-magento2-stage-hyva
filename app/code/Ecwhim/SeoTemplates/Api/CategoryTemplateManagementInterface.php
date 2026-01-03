<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Api;

/**
 * @api
 */
interface CategoryTemplateManagementInterface
{
    const ENTITY_TYPE_CATEGORY_TEMPLATE = 'ecwhim_seotemplates_category_template';

    /**
     * @param \Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface $template
     * @return bool
     */
    public function apply(\Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface $template): bool;
}

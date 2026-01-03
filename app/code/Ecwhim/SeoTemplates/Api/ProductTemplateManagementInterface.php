<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Api;

/**
 * @api
 */
interface ProductTemplateManagementInterface
{
    const ENTITY_TYPE_PRODUCT_TEMPLATE = 'ecwhim_seotemplates_product_template';

    /**
     * @param \Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterface $template
     * @return bool
     */
    public function apply(\Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterface $template): bool;
}

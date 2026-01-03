<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Controller\Adminhtml;

abstract class ProductTemplate extends \Magento\Backend\App\Action
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Ecwhim_SeoTemplates::seo_templates_product_template';
}

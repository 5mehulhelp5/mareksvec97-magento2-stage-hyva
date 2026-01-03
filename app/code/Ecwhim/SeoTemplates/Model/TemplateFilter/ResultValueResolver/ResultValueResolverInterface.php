<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model\TemplateFilter\ResultValueResolver;

/**
 * @api
 */
interface ResultValueResolverInterface
{
    /**
     * @param string $value
     * @return string
     */
    public function resolve(string $value): string;
}

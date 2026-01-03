<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model\TemplateFilter\ResultValueResolver;

class DefaultResolver implements ResultValueResolverInterface
{
    /**
     * @inheritDoc
     */
    public function resolve(string $value): string
    {
        return trim($value);
    }
}

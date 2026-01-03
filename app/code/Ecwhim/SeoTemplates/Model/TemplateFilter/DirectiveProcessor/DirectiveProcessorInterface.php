<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model\TemplateFilter\DirectiveProcessor;

/**
 * @api
 */
interface DirectiveProcessorInterface
{
    /**
     * @param string[] $construction
     * @param \Ecwhim\SeoTemplates\Model\TemplateFilter $filter
     * @return string
     */
    public function process(array $construction, \Ecwhim\SeoTemplates\Model\TemplateFilter $filter): string;

    /**
     * @return string
     */
    public function getRegularExpression(): string;
}

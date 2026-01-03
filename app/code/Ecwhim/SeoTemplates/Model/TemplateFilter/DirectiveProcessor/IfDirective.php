<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model\TemplateFilter\DirectiveProcessor;

class IfDirective implements DirectiveProcessorInterface
{
    /**
     * @inheritDoc
     */
    public function process(array $construction, \Ecwhim\SeoTemplates\Model\TemplateFilter $filter): string
    {
        $values = explode('||', $construction[1]);

        foreach ($values as $value) {
            if (preg_match_all($filter->getVarRegularExpression(), $value, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $replacedValue = $filter->filterUsingEntityVarDirectiveGroup($match[0]);

                    if (empty($replacedValue)) {
                        continue 2;
                    }

                    $value = str_replace($match[0], $replacedValue, $value);
                }
            }

            return $value;
        }

        return '';
    }

    /**
     * @inheritDoc
     */
    public function getRegularExpression(): string
    {
        return '/\[\[if\s(.*?)\]\]/si';
    }
}

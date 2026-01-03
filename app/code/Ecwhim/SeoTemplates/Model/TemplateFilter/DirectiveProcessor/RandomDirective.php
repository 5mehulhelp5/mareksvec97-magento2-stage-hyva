<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model\TemplateFilter\DirectiveProcessor;

class RandomDirective implements DirectiveProcessorInterface
{
    /**
     * @inheritDoc
     */
    public function process(array $construction, \Ecwhim\SeoTemplates\Model\TemplateFilter $filter): string
    {
        return $this->getValue(explode('||', $construction[1]), $filter);
    }

    /**
     * @inheritDoc
     */
    public function getRegularExpression(): string
    {
        return '/\[\[random\s(.*?)\]\]/si';
    }

    /**
     * @param string[] $values
     * @param \Ecwhim\SeoTemplates\Model\TemplateFilter $filter
     * @return string
     */
    private function getValue(array $values, \Ecwhim\SeoTemplates\Model\TemplateFilter $filter): string
    {
        $key   = 0;
        $count = count($values);

        if ($count > 1) {
            $key = rand(1, $count) - 1;
        }

        $value = $values[$key];

        if (preg_match_all($filter->getVarRegularExpression(), $value, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $replacedValue = $filter->filterUsingEntityVarDirectiveGroup($match[0]);

                if (empty($replacedValue)) {
                    unset($values[$key]);

                    if (empty($values)) {
                        return '';
                    }

                    return $this->getValue(array_values($values), $filter);
                }

                $value = str_replace($match[0], $replacedValue, $value);
            }
        }

        return $value;
    }
}

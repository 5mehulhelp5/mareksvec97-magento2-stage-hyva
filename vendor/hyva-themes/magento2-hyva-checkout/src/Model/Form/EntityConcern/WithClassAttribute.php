<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form\EntityConcern;

trait WithClassAttribute
{
    use WithAttributes;

    public function setClassAttributeValue(string $class, string $delimiter = ' '): self
    {
        if ($this->hasAttribute('class')) {
            $classes = explode($delimiter, $this->getAttributeValue('class'));
        }

        foreach (explode($delimiter, $class) as $value) {
            $classes[] = $value;
        }

        return $this->setAttribute('class', trim(implode(' ', array_unique(array_values($classes ?? [])))));
    }

    public function hasClassAttributeValue(?string $class = null): bool
    {
        $classes = $this->getClassAttributeValueAsArray();

        if ($class) {
            return in_array($class, $classes);
        }

        return count($classes) !== 0;
    }

    public function getClassAttributeValueAsArray(): array
    {
        return array_flip(explode(' ', $this->getClassAttributeValue()));
    }

    public function getClassAttributeValue(): string
    {
        return $this->getAttributeValue('class') ?? '';
    }
}

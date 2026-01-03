<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form\EntityConcern;

use Magento\Framework\Escaper;

trait WithAttributes
{
    /** @var string[] */
    protected array $attributes = [];
    /** @var array[] */
    protected array $sections = [];

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getAttributeSections(): array
    {
        return $this->sections;
    }

    public function hasAttributes(): bool
    {
        return count($this->getAttributes()) !== 0;
    }

    /**
     * @deprecated has been replaced with setAttribute method.
     */
    public function addAttribute($value, ?string $name = null): self
    {
        $name  = $name === null ? $value : $name;
        $value = $value === $name ? null : $value;

        return $this->setAttribute($name, $value);
    }

    public function setAttribute(string $name, $value = null): self
    {
        $this->attributes[$name] = $value;
        return $this;
    }

    public function setAttributeForSection(string $section, $attribute, $value): self
    {
        $this->sections[$section][$attribute] = $value;
        return $this;
    }

    public function getAttributeValue(string $name): ?string
    {
        return $this->attributes[$name] ?? null;
    }

    public function hasAttribute(string $name): bool
    {
        return array_key_exists($name, $this->attributes);
    }

    public function hasAttributesStartingWith(string $prefix): bool
    {
        return array_reduce(array_keys($this->attributes), fn ($carry, $key) => $carry || strpos($key, $prefix) === 0, false);
    }

    public function removeAttribute(string $name): self
    {
        unset($this->attributes[$name]);

        return $this;
    }

    public function replaceAttribute(string $from, string $to, $value = null): self
    {
        if ($this->hasAttribute($from)) {
            $value = $value ?? $this->attributes[$from];

            $this->removeAttribute($from)->setAttribute($to, $value);
        }

        return $this;
    }

    public function removeAttributesStartingWith(string $prefix)
    {
        $this->attributes = array_filter($this->attributes, fn ($attribute) => strpos($attribute, $prefix) !== 0, ARRAY_FILTER_USE_KEY);
    }

    public function renderAttributes(?Escaper $escaper = null, ?string $section = null): string
    {
        $attributes = $this->getAttributes();

        if ($section) {
            $attributes = $this->sections[$section] ?? [];
        }

        if (count($attributes) === 0) {
            return '';
        }

        return implode(' ', array_map(function ($attribute, ?string $value) use ($escaper) {
            return $value === null ? $attribute : sprintf('%s="%s"', $attribute, $escaper ? $escaper->escapeHtmlAttr($value) : $value);
        }, array_keys($attributes), array_values($attributes)));
    }
}

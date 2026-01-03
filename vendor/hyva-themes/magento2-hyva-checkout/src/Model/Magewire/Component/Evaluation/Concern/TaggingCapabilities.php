<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Magewire\Component\Evaluation\Concern;

trait TaggingCapabilities
{
    private array $tags = [];

    public function withTag(string $tag): self
    {
        $this->tags[] = $tag;

        return $this;
    }

    public function withTags(array $tags, bool $merge = true): self
    {
        $this->tags = $merge ? array_merge($this->tags, $tags) : $tags;

        return $this;
    }

    public function hasTags(): bool
    {
        return count($this->tags) !== 0;
    }

    public function containsTag(string $tag): bool
    {
        return in_array($tag, $this->tags);
    }

    public function containsTags(array $tags, bool $strict = false): bool
    {
        $search = array_filter($tags, fn ($tag) => in_array($tag, $this->tags, $strict));

        return $strict ? count($search) === 0 : count($search) > 0;
    }

    public function getTags(): array
    {
        return $this->tags;
    }
}

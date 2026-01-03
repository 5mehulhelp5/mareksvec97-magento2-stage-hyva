<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Magewire\Component\Evaluation\Concern;

trait NamingCapabilities
{
    private ?string $name = null;

    public function withName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function hasName(?string $name = null): bool
    {
        if ($name) {
            return $this->name === $name;
        }

        return is_string($this->name);
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}

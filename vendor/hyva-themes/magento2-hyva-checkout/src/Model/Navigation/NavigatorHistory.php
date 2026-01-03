<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Navigation;

use Hyva\Checkout\Model\Checkout;

class NavigatorHistory
{
    private array $movements = [];

    public function push(Checkout\Step $step)
    {
        $this->movements[] = $step;
    }

    public function refresh(): self
    {
        return $this->clear();
    }

    public function clear(): self
    {
        $this->movements = [];

        return $this;
    }

    public function getMovements(): array
    {
        return $this->movements;
    }

    public function getLatest(): ?Checkout\Step
    {
        $latest = end($this->movements);

        return $latest ?: null;
    }

    public function getFirst(): ?Checkout\Step
    {
        $first = reset($this->movements);

        return $first ?: null;
    }

    public function getBeforeLatest(): ?Checkout\Step
    {
        $i = count($this->movements);

        if ($i <= 1) {
            return null;
        }

        return $this->movements[$i - 2];
    }
}

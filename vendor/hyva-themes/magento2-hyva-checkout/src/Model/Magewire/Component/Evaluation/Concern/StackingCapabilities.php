<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Magewire\Component\Evaluation\Concern;

trait StackingCapabilities
{
    private int $stackPosition = 500;

    /**
     * @return static till PHP8.x
     * @doc https://github.com/php/php-src/pull/5062
     */
    public function withStackPosition(int $position): self
    {
        $this->stackPosition = $position;

        return $this;
    }

    public function getStackPosition(): int
    {
        return $this->stackPosition;
    }

    public function getDefaultStackPosition(): int
    {
        return 500;
    }

    public function resetStackPosition(): self
    {
        $this->stackPosition = $this->getDefaultStackPosition();

        return $this;
    }
}

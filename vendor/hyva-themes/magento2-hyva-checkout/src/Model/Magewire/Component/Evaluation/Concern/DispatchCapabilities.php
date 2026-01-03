<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Magewire\Component\Evaluation\Concern;

trait DispatchCapabilities
{
    private bool $dispatch = false;

    /**
     * Perform direct dispatching if immediate dispatching is supported.
     */
    public function dispatch(bool $dispatch = true): static
    {
        $this->dispatch = $dispatch;

        return $this;
    }

    /**
     * Suppress direct dispatching if immediate dispatching is supported.
     */
    public function revokeDispatch(): static
    {
        $this->dispatch = false;

        return $this;
    }

    public function canDispatch(): bool
    {
        return $this->dispatch;
    }
}

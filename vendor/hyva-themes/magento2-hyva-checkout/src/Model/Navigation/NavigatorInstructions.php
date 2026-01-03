<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Navigation;

use Magento\Framework\DataObject;

class NavigatorInstructions extends DataObject
{
    private ?string $checkout = null;
    private ?string $step = null;

    public function setCheckout(string $checkout): self
    {
        $this->checkout = $checkout;

        return $this;
    }

    public function getCheckout(): ?string
    {
        return $this->checkout;
    }

    public function hasCheckout(): bool
    {
        return is_string($this->checkout);
    }

    public function setStep(string $step): self
    {
        $this->step = $step;

        return $this;
    }

    public function getStep(): ?string
    {
        return $this->step;
    }

    public function hasStep(): bool
    {
        return is_string($this->step);
    }
}

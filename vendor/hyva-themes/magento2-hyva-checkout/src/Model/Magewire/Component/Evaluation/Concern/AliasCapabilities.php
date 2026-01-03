<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Magewire\Component\Evaluation\Concern;

trait AliasCapabilities
{
    private ?string $alias = null;

    /**
     * Set an alternative, non-public alias for internal use. This alias is primarily used for flagging specific results
     * that can be checked for existence internally without the risk of being displayed on the frontend.
     */
    public function withAlias(string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    public function hasAlias(?string $alias = null): bool
    {
        if ($alias) {
            return $this->alias === $alias;
        }

        return is_string($this->alias);
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }
}

<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Magewire\ServerMemoConfig;

use Hyva\Checkout\Model\Magewire\ServerMemoConfigInterface;

abstract class AbstractConfigSection implements ServerMemoConfigInterface
{
    private array $data;

    public function __construct(
        array $data = []
    ) {
        $this->data = $data;
    }

    abstract public function getData(): array;

    public function getDataInjection(): ?array
    {
        return empty($this->data) ? null : $this->data;
    }

    public function isStaticData(): bool
    {
        return true;
    }
}

<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form;

interface EntityFieldSelectInterface extends EntityFieldInterface
{
    public const TYPE = 'select';

    /**
     * Set (overwrite with custom-) options.
     */
    public function setOptions(array $options): self;

    /**
     * Get entity select options.
     */
    public function getOptions(): array;

    /**
     * Validate if entity has options to return.
     */
    public function hasOptions(): bool;

    /**
     * Unset all options.
     */
    public function clearOptions(): self;
}

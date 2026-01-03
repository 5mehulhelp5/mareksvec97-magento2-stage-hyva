<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\CheckoutInformation;

use Hyva\Checkout\Model\CheckoutInformationInterface;

/**
 * Makes the Luma checkout compatible and usable when selected.
 */
class Luma implements CheckoutInformationInterface
{
    public const NAMESPACE = 'magento_luma';

    public function getNamespace(): string
    {
        return self::NAMESPACE;
    }

    public function getLabel(): string
    {
        return 'Magento Luma (original)';
    }

    public function canApply(): bool
    {
        return true;
    }

    public function execute(callable $checkout)
    {
        return $checkout();
    }
}

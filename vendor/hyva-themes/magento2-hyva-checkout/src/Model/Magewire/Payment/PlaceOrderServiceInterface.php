<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Magewire\Payment;

use Exception;
use Magento\Quote\Model\Quote;
use Magewirephp\Magewire\Component;

/**
 * @deprecated use the AbstractPlaceOrderService instead.
 * @see \Hyva\Checkout\Model\Magewire\Payment\AbstractPlaceOrderService
 */
interface PlaceOrderServiceInterface
{
    public function placeOrder(Quote $quote): int;

    public function canPlaceOrder(): bool;

    public function handleException(Exception $exception, Component $component, Quote $quote): void;

    public function canRedirect(): bool;

    public function getRedirectUrl(Quote $quote, ?int $orderId = null): string;
}

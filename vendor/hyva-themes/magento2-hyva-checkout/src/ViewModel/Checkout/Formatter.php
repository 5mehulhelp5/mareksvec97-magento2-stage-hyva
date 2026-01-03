<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\ViewModel\Checkout;

use Magento\Framework\Phrase;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Formatter implements ArgumentInterface
{
    protected PriceCurrencyInterface $priceCurrency;

    /**
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * Applies currency formatting.
     */
    public function currency(
        $amount,
        bool $includeContainer = false,
        int $precision = PriceCurrencyInterface::DEFAULT_PRECISION
    ): string {
        return $this->priceCurrency->format($amount, $includeContainer, $precision);
    }

    /**
     * Applies currency formatting with optional condition-based overrides.
     */
    public function currencyWithLabelingConditions(
        $amount,
        string $area = 'global',
        bool $includeContainer = false,
        int $precision = PriceCurrencyInterface::DEFAULT_PRECISION,
        array $conditions = []
    ): string {
        foreach ($this->filterCurrencyConditions($conditions) as $condition) {
            $result = $condition((float) $amount, $area, $conditions);

            if ($result !== $amount) {
                return match (true) {
                    is_string($result) => __($result)->render(),
                    $result instanceof Phrase => $result->render(),
                    default => $this->currency($amount, $includeContainer, $precision),
                };
            }
        }

        return $this->currency($amount, $includeContainer, $precision);
    }

    /**
     * Handles the mapping of currency conditions, intended for use in before or after plugins.
     * Allows adding or removing specific conditions from either injected sources or the provided mergeable array.
     *
     * Also, it makes sure all values are actually callables.
     *
     * @param array<string|int, callable> $mergeWith Optional array of conditions to merge with.
     * @return array The modified array of currency conditions.
     */
    public function filterCurrencyConditions(array $mergeWith = []): array
    {
        return array_filter($mergeWith, 'is_callable');
    }
}

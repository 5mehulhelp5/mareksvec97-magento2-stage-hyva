<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */
declare(strict_types=1);

namespace Hyva\CheckoutPayPal\ViewModel\Payment;

use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class CreditCardForm implements ArgumentInterface
{
    private JsonSerializer $jsonSerializer;

    private array $creditCardTypesJsConfig;

    public function __construct(
        JsonSerializer $jsonSerializer,
        array $creditCardTypesJsConfig = []
    ) {
        $this->jsonSerializer = $jsonSerializer;
        $this->creditCardTypesJsConfig = $creditCardTypesJsConfig;
    }

    public function getCreditCardTypesJsConfig(array $allowedTypes = []): string
    {
        $allowedCardTypesJsConfig = $this->creditCardTypesJsConfig;

        if ($allowedTypes) {
            $allowedCardTypesJsConfig = array_filter(
                $allowedCardTypesJsConfig,
                fn (array $cardTypeConfig) => in_array($cardTypeConfig['type'] ?? '', $allowedTypes, false)
            );
        }

        return $this->jsonSerializer->serialize(array_values($allowedCardTypesJsConfig)) ?: '[]';
    }
}

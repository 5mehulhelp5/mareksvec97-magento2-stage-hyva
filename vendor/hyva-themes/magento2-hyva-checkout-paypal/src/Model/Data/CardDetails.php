<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */
declare(strict_types=1);

namespace Hyva\CheckoutPayPal\Model\Data;

class CardDetails
{
    private string $cardType;

    private string $cardLast4;

    private string $expirationMonth;

    private string $expirationYear;

    public function __construct(
        string $cardType,
        string $cardLast4,
        string $expirationMonth,
        string $expirationYear
    ) {
        $this->cardType = $cardType;
        $this->cardLast4 = $cardLast4;
        $this->expirationMonth = $expirationMonth;
        $this->expirationYear = $expirationYear;
    }

    public function getCardType(): string
    {
        return $this->cardType;
    }

    public function getCardLast4(): string
    {
        return $this->cardLast4;
    }

    public function getExpirationMonth(): string
    {
        return $this->expirationMonth;
    }

    public function getExpirationYear(): string
    {
        return $this->expirationYear;
    }

    public function hasEmptyData(): bool
    {
        return !$this->getCardType() ||
            !$this->getCardLast4() ||
            !$this->getExpirationMonth() ||
            !$this->getExpirationYear();
    }
}

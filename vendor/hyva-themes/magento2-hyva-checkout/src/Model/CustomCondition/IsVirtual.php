<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\CustomCondition;

use Hyva\Checkout\Model\CustomConditionInterface;
use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @api
 */
class IsVirtual implements CustomConditionInterface
{
    protected SessionCheckout $sessionCheckout;

    public function __construct(
        SessionCheckout $sessionCheckout
    ) {
        $this->sessionCheckout = $sessionCheckout;
    }

    /**
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function validate(): bool
    {
        return $this->sessionCheckout->getQuote()->isVirtual();
    }
}

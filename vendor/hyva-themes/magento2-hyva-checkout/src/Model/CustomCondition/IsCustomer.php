<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\CustomCondition;

use Magento\Checkout\Helper\Data as HelperCheckoutData;
use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @api
 */
class IsCustomer extends IsGuest
{
    protected HelperCheckoutData $helperCheckoutData;

    public function __construct(
        SessionCheckout $sessionCheckout,
        HelperCheckoutData $helperCheckoutData
    ) {
        parent::__construct($sessionCheckout);

        $this->helperCheckoutData = $helperCheckoutData;
    }

    /**
     * Validate if customer is not a guest (logged in).
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function validate(): bool
    {
        return ! parent::validate();
    }

    /**
     * Validate if customer is required to log in.
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function required(): bool
    {
        return ! $this->helperCheckoutData->isAllowedGuestCheckout($this->sessionCheckout->getQuote());
    }
}

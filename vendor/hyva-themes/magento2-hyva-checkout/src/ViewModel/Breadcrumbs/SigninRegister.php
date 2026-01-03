<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\ViewModel\Breadcrumbs;

use Magento\Customer\Model\Session as SessionCustomer;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class SigninRegister implements ArgumentInterface
{
    protected SessionCustomer $sessionCustomer;

    /**
     * @param SessionCustomer $sessionCustomer
     */
    public function __construct(
        SessionCustomer $sessionCustomer
    ) {
        $this->sessionCustomer = $sessionCustomer;
    }

    /**
     * @return SessionCustomer
     */
    public function getCustomerSession(): SessionCustomer
    {
        return $this->sessionCustomer;
    }
}

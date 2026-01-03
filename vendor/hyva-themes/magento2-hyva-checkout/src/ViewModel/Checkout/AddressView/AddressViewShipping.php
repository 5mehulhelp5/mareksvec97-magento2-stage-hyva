<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\ViewModel\Checkout\AddressView;

use Hyva\Checkout\Model\Component\AddressTypeManagement;
use Hyva\Checkout\ViewModel\Checkout\AddressView;
use Magento\Checkout\Model\Session as SessionCheckout;
use Psr\Log\LoggerInterface;

class AddressViewShipping extends AddressView
{
    public function __construct(
        SessionCheckout $sessionCheckout,
        AddressTypeManagement $addressTypeManager,
        LoggerInterface $logger
    ) {
        parent::__construct($sessionCheckout, $addressTypeManager->getAddressTypeShipping(), $logger);
    }
}

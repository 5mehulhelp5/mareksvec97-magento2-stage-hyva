<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\ViewModel\Checkout\AddressView\AddressList;

use Hyva\Checkout\Model\Component\AddressTypeManagement;
use Hyva\Checkout\ViewModel\Checkout\AddressRenderer;
use Hyva\Checkout\ViewModel\Checkout\AddressView\AbstractAddressList;
use Magento\Customer\Model\Session as SessionCustomer;
use Psr\Log\LoggerInterface;

class AddressListShipping extends AbstractAddressList
{
    public function __construct(
        SessionCustomer $sessionCustomer,
        AddressRenderer $addressRenderer,
        LoggerInterface $logger,
        AddressTypeManagement $addressTypeManager
    ) {
        parent::__construct(
            $sessionCustomer,
            $addressRenderer,
            $logger,
            $addressTypeManager->getAddressTypeShipping()
        );
    }
}

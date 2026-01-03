<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Magewire\Checkout;

use Magento\Customer\Model\Session as SessionCustomer;
use Magento\Framework\UrlInterface;
use Magewirephp\Magewire\Component;

class CustomerLogin extends Component
{
    public function __construct(
        SessionCustomer $sessionCustomer,
        UrlInterface $url
    ) {
        // Let's make sure the customer gets redirected back to the checkout at all stages.
        $sessionCustomer->setBeforeAuthUrl(
            $url->getUrl('checkout', ['_current' => true])
        );
    }
}

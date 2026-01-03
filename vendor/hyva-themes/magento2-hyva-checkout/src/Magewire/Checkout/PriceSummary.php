<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Magewire\Checkout;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\ShippingMethodManagementInterface;
use Magewirephp\Magewire\Component;
use Magento\Checkout\Model\Session as SessionCheckout;

class PriceSummary extends Component
{
    protected SessionCheckout $sessionCheckout;
    protected ShippingMethodManagementInterface $shippingMethodManagement;

    protected $listeners = [
        'shipping_method_selected' => 'refresh',
        'payment_method_selected' => 'refresh',
        'coupon_code_applied' => 'refresh',
        'coupon_code_revoked' => 'refresh',
        'shipping_address_saved' => 'refresh',
        'shipping_address_activated' => 'refresh',
        'billing_address_saved' => 'refresh',
        'billing_address_activated' => 'refresh'
    ];

    public function __construct(
        SessionCheckout $sessionCheckout,
        ?ShippingMethodManagementInterface $shippingMethodManagement = null
    ) {
        $this->sessionCheckout = $sessionCheckout;

        $this->shippingMethodManagement = $shippingMethodManagement
            ?: ObjectManager::getInstance()->get(ShippingMethodManagementInterface::class);
    }

    /**
     * @deprecated This method has been deprecated, and its body has been
     *             intentionally emptied to prevent unintended execution.
     */
    // phpcs:ignore
    public function refresh()
    {
        //
    }
}

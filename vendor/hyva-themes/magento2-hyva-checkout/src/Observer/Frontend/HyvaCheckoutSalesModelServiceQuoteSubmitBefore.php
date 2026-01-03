<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Observer\Frontend;

use Hyva\Checkout\Model\ConfigData\HyvaThemes\Developer\SystemConfigFixesWorkarounds;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;

class HyvaCheckoutSalesModelServiceQuoteSubmitBefore implements ObserverInterface
{
    private SystemConfigFixesWorkarounds $systemConfigFixesWorkarounds;
    private array $customerNameFields;

    public function __construct(
        SystemConfigFixesWorkarounds $systemConfigFixesWorkarounds,
        array $customerNameFields = []
    ) {
        $this->systemConfigFixesWorkarounds = $systemConfigFixesWorkarounds;
        $this->customerNameFields = $customerNameFields;
    }

    /**
     * Ensure guest customer name data exists in both quote and order.
     */
    public function execute(Observer $observer): void
    {
        if (! $this->systemConfigFixesWorkarounds->applyMigrateGuestInfo()) {
            return;
        }

        /** @var Quote $quote */
        $quote = $observer->getData('quote');
        /** @var Order $order */
        $order = $observer->getData('order');

        $shippingAddress = $quote->getShippingAddress();

        foreach ($this->customerNameFields as $customerNameField) {
            $prefixedNameField = 'customer_' . $customerNameField;

            // Set data for the Quote object.
            if (! $quote->getData($prefixedNameField)) {
                $quote->setData($prefixedNameField, $shippingAddress->getData($customerNameField));
            }

            // Set data for the order object.
            if (! $order->getData($prefixedNameField)) {
                $order->setData($prefixedNameField, $shippingAddress->getData($customerNameField));
            }
        }
    }
}

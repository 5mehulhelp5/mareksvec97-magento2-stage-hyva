<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Observer\Frontend;

use Hyva\Checkout\Model\Navigation\Navigator;
use Hyva\Checkout\Model\Session as SessionCheckoutConfig;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class HyvaCheckoutSessionReset implements ObserverInterface
{
    protected SessionCheckoutConfig $sessionCheckoutConfig;

    private Navigator $navigator;

    public function __construct(
        SessionCheckoutConfig $sessionCheckoutConfig,
        ?Navigator $navigator = null
    ) {
        $this->sessionCheckoutConfig = $sessionCheckoutConfig;

        $this->navigator = $navigator
            ?: ObjectManager::getInstance()->get(Navigator::class);
    }

    // phpcs:ignore
    public function execute(Observer $observer): void
    {
        $this->navigator->reset();
        $this->navigator->getMemory()->destroy();
    }
}

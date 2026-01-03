<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Observer\Frontend;

use Hyva\Checkout\Model\Config as HyvaCheckoutConfig;
use Hyva\Checkout\Model\Navigation\Navigator;
use Hyva\Checkout\Model\Session as SessionCheckoutConfig;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Page\Config;

class HyvaCheckoutLayoutLoadBefore implements ObserverInterface
{
    protected Config $pageConfig;
    protected SessionCheckoutConfig $sessionCheckoutConfig;
    protected HyvaCheckoutConfig $hyvaCheckoutConfig;

    private Navigator $navigator;

    public function __construct(
        Context $context,
        SessionCheckoutConfig $sessionCheckoutConfig,
        HyvaCheckoutConfig $hyvaCheckoutConfig,
        ?Navigator $navigator = null
    ) {
        $this->pageConfig = $context->getPageConfig();
        $this->sessionCheckoutConfig = $sessionCheckoutConfig;
        $this->hyvaCheckoutConfig = $hyvaCheckoutConfig;

        $this->navigator = $navigator
            ?: Objectmanager::getInstance()->get(Navigator::class);
    }

    public function execute(Observer $observer): void
    {
        if ($observer->getData('full_action_name') !== 'hyva_checkout_index_index') {
            return ;
        }

        $activeCheckoutNamespace = $this->navigator->getActiveCheckout()->getname();

        if ($activeCheckoutNamespace
            && $this->hyvaCheckoutConfig->isHyvaCheckout($activeCheckoutNamespace)
        ) {
            $checkout = $this->hyvaCheckoutConfig->getDataByPath([$activeCheckoutNamespace]);

            if ($checkout && isset($checkout['sequence'])) {
                foreach ($checkout['sequence'] as $checkout) {
                    $this->pageConfig->addBodyClass('checkout-' . $checkout);
                }
            }
        }
    }
}

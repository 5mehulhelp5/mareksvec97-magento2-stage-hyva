<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\CheckoutPayPal\Observer\Frontend;

use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class HyvaCheckoutHyvaConfigGenerateBefore implements ObserverInterface
{
    protected ComponentRegistrar $componentRegistrar;

    public function __construct(ComponentRegistrar $componentRegistrar)
    {
        $this->componentRegistrar = $componentRegistrar;
    }

    public function execute(Observer $observer)
    {
        $config = $observer->getData('config');
        $extensions = $config->hasData('extensions') ? $config->getData('extensions') : [];
        $moduleName = implode('_', array_slice(explode('\\', __CLASS__), 0, 2));
        $path = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, $moduleName);

        // Only use the path relative to the Magento base dir.
        $extensions[] = ['src' => substr($path, strlen(BP) + 1)];

        $config->setData('extensions', $extensions);
    }
}

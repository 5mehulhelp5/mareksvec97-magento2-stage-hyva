<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\ViewModel;

use Hyva\Checkout\Model\Checkout\Step;
use Hyva\Checkout\Model\Navigation\Navigator;
use Hyva\Checkout\Model\Session as SessionCheckoutConfig;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Main implements ArgumentInterface
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

    public function getStepClasses(): array
    {
        $step = $this->navigator->getActiveStep();

        if ($step) {
            $classes['step_name'] = 'step-' . $step->getName();
            $layoutUpdates = $step->getUpdates(Step::UPDATE_TYPE_LAYOUT);

            if (count($layoutUpdates) && isset($layoutUpdates['handle'])) {
                $classes['step_layout'] = 'step-layout-' . str_replace(['hyva_checkout_layout_', '_'], ['', '-'], $layoutUpdates['handle']);
            }
        }

        return $classes ?? [];
    }

    public function getStepClassesAsString(): string
    {
        return trim(implode(' ', $this->getStepClasses()));
    }
}

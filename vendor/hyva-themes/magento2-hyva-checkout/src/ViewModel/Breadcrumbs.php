<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\ViewModel;

use Hyva\Checkout\Model\ConfigData\HyvaThemes\Checkout as SystemCheckoutConfig;
use Hyva\Checkout\Model\Navigation\Navigator;
use Hyva\Checkout\Model\Session as SessionCheckoutConfig;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Breadcrumbs implements ArgumentInterface
{
    protected SystemCheckoutConfig $systemCheckoutConfig;

    private ?Navigator $navigator;

    /**
     * @deprecated navigating based on session configuration was replaced by a native navigator object.
     * @see Navigator
     */
    protected SessionCheckoutConfig $sessionCheckoutConfig;

    public function __construct(
        SessionCheckoutConfig $sessionCheckoutConfig,
        SystemCheckoutConfig $systemCheckoutConfig,
        ?Navigator $navigator = null
    ) {
        $this->sessionCheckoutConfig = $sessionCheckoutConfig;
        $this->systemCheckoutConfig = $systemCheckoutConfig;

        $this->navigator = $navigator
            ?: Objectmanager::getInstance()->get(Navigator::class);
    }

    public function getNavigator(): Navigator
    {
        return $this->navigator;
    }

    /**
     * @deprecated navigating based on session configuration was replaced by a native navigator object.
     * @see Navigator
     */
    public function getCheckoutConfig(): SessionCheckoutConfig
    {
        return $this->sessionCheckoutConfig;
    }

    public function getSystemConfig(): SystemCheckoutConfig
    {
        return $this->systemCheckoutConfig;
    }
}

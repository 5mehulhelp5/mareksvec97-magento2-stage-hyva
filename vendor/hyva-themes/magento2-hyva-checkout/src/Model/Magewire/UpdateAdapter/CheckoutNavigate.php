<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Magewire\UpdateAdapter;

use Hyva\Checkout\Magewire\Main;
use Hyva\Checkout\Model\Magewire\Component\MainInterface;
use Hyva\Checkout\Model\Magewire\UpdateAdapterInterface;
use Hyva\Checkout\Model\Session as SessionCheckoutConfig;
use Magewirephp\Magewire\Model\RequestInterface;

class CheckoutNavigate implements UpdateAdapterInterface
{
    protected SessionCheckoutConfig $sessionCheckoutConfig;

    public function __construct(
        SessionCheckoutConfig $sessionCheckoutConfig
    ) {
        $this->sessionCheckoutConfig = $sessionCheckoutConfig;
    }

    public function belongsToNavigationComponent(RequestInterface $request): bool
    {
        return $request->getFingerprint('type') === Main::COMPONENT_TYPE;
    }

    public function isNavigationUpdateRequest(array $update): bool
    {
        return $update['type'] === 'callMethod'
            && isset($update['payload']['method'])
            && $update['payload']['method'] === MainInterface::METHOD_NAVIGATE;
    }

    public function locateStep(array $update): ?string
    {
        return $update['payload']['params'][0] ?? null;
    }
}

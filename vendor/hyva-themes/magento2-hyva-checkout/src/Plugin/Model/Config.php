<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Plugin\Model;

use Hyva\Checkout\Model\Config as Subject;
use Hyva\Checkout\Model\Session as SessionCheckoutConfig;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\State as ApplicationState;

class Config
{
    protected ApplicationState $applicationState;
    protected RequestInterface $request;
    protected SessionCheckoutConfig $sessionCheckoutConfig;

    public function __construct(
        ApplicationState $applicationState,
        RequestInterface $request,
        SessionCheckoutConfig $sessionCheckoutConfig
    ) {
        $this->applicationState = $applicationState;
        $this->request = $request;
        $this->sessionCheckoutConfig = $sessionCheckoutConfig;
    }

    public function afterGetActiveCheckoutNamespace(Subject $subject, string $result): string
    {
        if ($this->applicationState->getMode() === ApplicationState::MODE_DEVELOPER) {
            $checkout = $this->request->getParam('checkout', $result);
            $checkouts = $subject->getList();

            if (array_key_exists($checkout, $checkouts)) {
                if ($this->sessionCheckoutConfig->getHash() !== $checkouts[$checkout]['hash'] ?? null) {
                    $this->sessionCheckoutConfig->reset();
                }

                return $checkout;
            }
        }

        return $result;
    }
}

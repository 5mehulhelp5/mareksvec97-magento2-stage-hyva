<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Magewire\ServerMemoConfig;

use Hyva\Checkout\Model\Session as SessionCheckoutConfig;
use Hyva\Checkout\Model\Navigation\Navigator;
use Magento\Framework\App\ObjectManager;

class StepHistory extends AbstractConfigSection
{
    protected SessionCheckoutConfig $sessionCheckoutConfig;
    private Navigator $navigator;

    public function __construct(
        SessionCheckoutConfig $sessionCheckoutConfig,
        ?Navigator $navigator = null,
        array $data = []
    ) {
        parent::__construct($data);

        $this->sessionCheckoutConfig = $sessionCheckoutConfig;

        $this->navigator = $navigator
            ?: ObjectManager::getInstance()->get(Navigator::class);
    }

    public function getData(): array
    {
        $checkout = $this->navigator->getActiveCheckout();

        /*
         * Since this is a single-step process, we can't consult the navigator history because no prior navigation events
         * occurred during this step. As a result, it's safe to retrieve the first step from the active checkout and set
         * the previous step to null.
         */
        if ($checkout->isSingleStepper()) {
            return [
                'current'  => $checkout->getFirstStep()->toPublicDataArray(),
                'previous' => null,

                'steps' => [
                    'next' => null,
                    'before' => null
                ]
            ];
        }

        $current = $this->navigator->getHistory()->getLatest();
        $data['current'] = $current->toPublicDataArray();

        $previous = $this->navigator->getHistory()->getBeforeLatest();
        $data['previous'] = $previous ? $previous->toPublicDataArray() : null;

        $next = $this->navigator->getActiveCheckout()->getStepAfter($current);
        $data['steps']['next'] = $next ? $next->toPublicDataArray() : null;

        $before = $this->navigator->getActiveCheckout()->getStepBefore($current);
        $data['steps']['before'] = $before ? $before->toPublicDataArray() : null;

        return $data;
    }

    public function isStaticData(): bool
    {
        return false;
    }
}

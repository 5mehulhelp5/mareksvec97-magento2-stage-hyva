<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Magewire\Hydrator;

use Hyva\Checkout\Model\Navigation\Navigator;
use Magewirephp\Magewire\Component;
use Magewirephp\Magewire\Model\HydratorInterface;
use Magewirephp\Magewire\Model\RequestInterface;
use Magewirephp\Magewire\Model\ResponseInterface;

class Navigation implements HydratorInterface
{
    private Navigator $navigator;

    public function __construct(
        Navigator $navigator
    ) {
        $this->navigator = $navigator;
    }

    // phpcs:ignore
    public function hydrate(Component $component, RequestInterface $request): void
    {
    }

    public function dehydrate(Component $component, ResponseInterface $response): void
    {
        if ($this->navigator->isRunning()) {
            $response->memo['navigation'] = [
                'checkout' => $this->navigator->getActiveCheckout()->getName(),
                'step' => $this->navigator->getActiveStep()->getRoute(),
            ];
        }
    }
}

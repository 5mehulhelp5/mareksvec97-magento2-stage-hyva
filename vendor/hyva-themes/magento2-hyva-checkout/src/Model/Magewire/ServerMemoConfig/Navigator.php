<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Magewire\ServerMemoConfig;

use Hyva\Checkout\Model\Checkout\Step;

class Navigator extends AbstractConfigSection
{
    private \Hyva\Checkout\Model\Navigation\Navigator $navigator;

    public function __construct(
        \Hyva\Checkout\Model\Navigation\Navigator $navigator,
        array $data = []
    ) {
        parent::__construct($data);

        $this->navigator = $navigator;
    }

    public function getData(): array
    {
        $beforeLatestStep = $this->navigator->getHistory()->getBeforeLatest();

        $data = [
            'finished' => $this->navigator->isFinished(),

            // Set Navigator history data.
            'history' => [
                'movements' => array_map(fn (Step $step) => $step->toPublicDataArray(), $this->navigator->getHistory()->getMovements()),
                'current' => $this->navigator->getActiveStep()->toPublicDataArray(),
                'previous' => null
            ],

            // Set Navigator checkout data.
            'checkout' => [
                'name' => $this->navigator->getActiveCheckout()->getName()
            ],

            // Set Navigator step data.
            'step' => $this->navigator->getActiveStep()->toPublicDataArray()
        ];

        if ($beforeLatestStep) {
            $data['history']['previous'] = $beforeLatestStep->toPublicDataArray();
        }

        return $data;
    }

    public function isStaticData(): bool
    {
        return false;
    }
}

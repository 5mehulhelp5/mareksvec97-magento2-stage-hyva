<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Magewire\Component\Evaluation;

use Hyva\Checkout\Model\Magewire\Component\Evaluation\Concern\DetailsCapabilities;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\Concern\DispatchCapabilities;
use Magewirephp\Magewire\Component;

class Event extends EvaluationResult
{
    use DetailsCapabilities;
    use DispatchCapabilities;

    public const TYPE = 'event';
    public const EVENT = 'evaluation:event:default';

    private ?string $event = null;

    public function getArguments(Component $component): array
    {
        return [
            'event' => $this->getEvent(),
            'detail' => $this->getDetails($component),

            // @deprecated the 'dispatch' property is now included in each result by default as the root argument.
            'dispatch' => $this->canDispatch(),
        ];
    }

    public function withCustomEvent(string $name): self
    {
        $this->event = $name;

        return $this;
    }

    public function getEvent(): string
    {
        return $this->event ?? $this::EVENT;
    }
}

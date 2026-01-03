<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Magewire\Component\Evaluation;

use Hyva\Checkout\Model\Magewire\Component\Evaluation\Concern\BlockingCapabilities;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\Concern\DispatchCapabilities;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\Concern\MessagingCapabilities;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\Concern\VisibilityCapabilities;
use Magewirephp\Magewire\Component;

class ErrorMessage extends EvaluationResult
{
    use BlockingCapabilities;
    use MessagingCapabilities;
    use DispatchCapabilities;
    use VisibilityCapabilities;

    public const TYPE = 'error_message';

    public function getArguments(Component $component): array
    {
        return [
            'text' => __($this->messageText),
            'title' => is_string($this->messageTitle) ? __($this->messageTitle) : $this->messageTitle,
            'type' => $this->messageType,
            'duration' => $this->messageVisibilityDuration,

            // @deprecated the 'dispatch' property is now included in each result by default as the root argument.
            'dispatch' => $this->canDispatch(),
        ];
    }

    public function getResult(): bool
    {
        return false;
    }
}

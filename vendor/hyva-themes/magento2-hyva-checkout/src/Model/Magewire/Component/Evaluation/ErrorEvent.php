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
use Magewirephp\Magewire\Component;

class ErrorEvent extends Event
{
    use BlockingCapabilities;

    // Default event to listen for.
    public const EVENT = 'evaluation:event:error';

    public function getResult(): bool
    {
        return false;
    }

    public function getArguments(Component $component): array
    {
        $arguments = parent::getArguments($component);

        /**
         * @deprecated you can no longer rely on this blocking entry from a frontend perspective.
         * @see Blocking
         */
        $arguments['blocking'] = $this->getBlockingArguments();

        /**
         * @deprecated the cause for blocking now lives in the 'blocking' arguments.
         * @see Blocking
         */
        if ($this->isBlocking()) {
            $arguments['cause'] = $this->cause;
        }

        return $arguments;
    }
}

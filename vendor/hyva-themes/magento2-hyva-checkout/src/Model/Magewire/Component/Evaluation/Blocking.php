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
use Hyva\Checkout\Model\Magewire\Component\Evaluation\Concern\DetailsCapabilities;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\Concern\DispatchCapabilities;
use Magewirephp\Magewire\Component;

/**
 * @deprecated This blocking evaluation result is deprecated as it silently obstructs primary navigation buttons.
 *             These buttons are essential for triggering navigational and validation tasks, which provide
 *             user-friendly notifications to guide customers on the next steps, such as proceeding or placing an order.
 * @see ErrorEventMessage, ErrorEvent, MessageDialog
 */
class Blocking extends ErrorMessage
{
    use BlockingCapabilities;
    use DetailsCapabilities;
    use DispatchCapabilities;

    public function getArguments(Component $component): array
    {
        $arguments = parent::getArguments($component);

        // Making the cause backward compatible when it was used on a blocking result.
        $cause = $this->hasCause() ? $this->getCause() : 'Something needs your attention before you can proceed with checkout.';

        return array_merge($arguments, ['text' => __($cause)]);
    }
}

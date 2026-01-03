<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Magewire\Component\Evaluation;

use Hyva\Checkout\Model\ConfigData\HyvaThemes\Developer\SystemConfigEvaluationApi;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\Concern\MessagingCapabilities;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\Concern\VisibilityCapabilities;
use Magewirephp\Magewire\Component;

class ErrorEventMessage extends ErrorEvent
{
    use MessagingCapabilities;
    use VisibilityCapabilities;

    private SystemConfigEvaluationApi $systemConfigEvaluationApi;

    public function __construct(
        SystemConfigEvaluationApi $systemConfigEvaluationApi
    ) {
        $this->systemConfigEvaluationApi = $systemConfigEvaluationApi;
    }

    public function getDetails(Component $component): array
    {
        $details = parent::getDetails($component);
        $showErrorAsWarning = $this->systemConfigEvaluationApi->displayErrorAsWarning();

        return $details + [
            'message' => [
                'text' => __($this->messageText),
                'type' => $this->messageType === 'error' && $showErrorAsWarning ? 'warning' : $this->messageType,
                'duration' => $this->messageVisibilityDuration,
                'title' => $this->hasMessageTitle() ? __($this->getMessageTitle()) : null,
            ]
        ];
    }
}

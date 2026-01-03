<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Magewire\Component\Evaluation;

use Hyva\Checkout\Model\Magewire\Component\Evaluation\Concern\MessagingCapabilities;
use Magento\Framework\App\State as ApplicationState;
use Magewirephp\Magewire\Component;

class BrowserConsoleLog extends Executable
{
    use MessagingCapabilities;

    private bool $logParams = false;

    private array $states = [
        0 => ApplicationState::MODE_DEVELOPER // By default, only visible in developer mode.
    ];

    public function __construct(
        string $message,
        private readonly ApplicationState $applicationState
    ) {
        $this->withMessage($message)->dispatch();

        parent::__construct('checkout.browser.console-log');
    }

    public function getArguments(Component $component): array
    {
        $arguments = [
            'dispatch' => false,
            ...parent::getArguments($component)
        ];

        if (in_array($this->applicationState->getMode(), $this->states, true)) {
            $arguments['dispatch'] = true;
            $arguments['variant']  = $this->messageType;
            $arguments['message']  = $this->getMessage();

            $arguments['params_log'] = $this->logParams;
        }

        return $arguments;
    }

    public function getMessage(): string
    {
        $message = strlen($this->messageText) === 0 ? 'No log message available.' : $this->messageText;

        if ($this->hasMessageTitle()) {
            return $this->getMessageTitle() . ': ' . $message;
        }

        return $message;
    }

    /**
     * Enable or disable logging of parameters to the console.
     */
    public function canLogParams(bool $choice): static
    {
        $this->logParams = $choice;

        return $this;
    }

    /**
     * Flag browser console-log to be both visible in developer and production mode.
     */
    public function useInProductionModeAlso(): static
    {
        $this->states = [
            ApplicationState::MODE_DEVELOPER,
            ApplicationState::MODE_PRODUCTION
        ];

        return $this;
    }

    /**
     * Flag browser console-log to only be visible in developer mode.
     */
    public function useInDeveloperModeOnly(): static
    {
        $this->states = [
            ApplicationState::MODE_DEVELOPER
        ];

        return $this;
    }
}

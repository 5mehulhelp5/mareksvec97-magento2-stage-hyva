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
use Hyva\Checkout\Model\Magewire\Component\Evaluation\Concern\ResultCapabilities;
use Hyva\Checkout\Model\Magewire\Hydrator\Evaluation;
use Magewirephp\Magewire\Component;

class MessageDialog extends EvaluationResult
{
    use MessagingCapabilities;
    use ResultCapabilities;

    public const TYPE = 'message_dialog';

    private ?Executable $confirmationCallback = null;
    private bool $cancelable = true;

    public function __construct(
        string $title,
        private readonly Evaluation $evaluationHydrator
    ) {
        $this->messageTitle = $title;
    }

    public function getArguments(Component $component): array
    {
        $arguments = [
            'title' => $this->hasMessageTitle() ? __($this->getMessageTitle()) : null,
            'text' => __($this->messageText),
            'type' => $this->messageType,
            'cancelable' => $this->cancelable
        ];

        if ($this->confirmationCallback) {
            $arguments['callback'] = $this->evaluationHydrator->compileEvaluationResult($component, $this->confirmationCallback);
        }

        return $arguments;
    }

    /**
     * Assigns an `Executable` instance as a confirmation callback,
     * which will be triggered when confirmation is needed.
     */
    public function withConfirmationCallback(Executable $callback): self
    {
        $this->confirmationCallback = $callback;

        return $this;
    }

    public function hasConfirmationCallback(): bool
    {
        return $this->confirmationCallback !== null;
    }

    public function getConfirmationCallback(): ?Executable
    {
        return $this->confirmationCallback;
    }

    /**
     * Sets the dialog message type to 'success'.
     */
    public function asSuccess(): self
    {
        $this->messageType = 'success';

        return $this;
    }

    /**
     * Flags the dialog to be cancelable or not.
     */
    public function canCancel(bool $cancelable = true): static
    {
        $this->cancelable = $cancelable;

        return $this;
    }

    /**
     * Presets the current message dialog to display a technical malfunction message.
     *
     * Note: It’s recommended to call this method first when configuring a technical malfunction dialog.
     * Additional customizations can be applied afterwards if needed.
     *
     * Example:
     *   $dialog->presetAsTechnicalMalfunction()
     *          ->withMessageTitle('Custom Title');
     */
    public function presetAsTechnicalMalfunction(): static
    {
        return $this

            // By default, a technical malfunction is shown as an error typed dialog.
            ->asError()
            // Make the dialog not cancelable.
            ->canCancel(false)
            // Technical malfunction dialogs are always aliased the same.
            ->withAlias('technical-malfunction-message-dialog')
            // A general but friendly malfunction message letting the user know something went wrong.
            ->withMessage('We\'re sorry this happened. It looks like something did not go as expected. To continue, press OK — this will reload the page automatically and try to resolve the issue.')
            // A confirmation callback that reloads the page to try and fix the problem.
            ->withConfirmationCallback(
                $this->factory()->createExecutable('checkout.page.reload')
            );
    }
}

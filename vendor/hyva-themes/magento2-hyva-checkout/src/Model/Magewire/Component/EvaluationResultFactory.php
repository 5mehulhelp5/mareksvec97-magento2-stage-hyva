<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Magewire\Component;

use Hyva\Checkout\Model\Magewire\Component\Evaluation\Batch;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\BrowserConsoleLog;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\Custom;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\ErrorEvent;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\ErrorEventMessage;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\ErrorMessage;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\Blocking;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\EvaluationResult;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\Event;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\Executable;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\MessageDialog;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\NavigationTask;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\Redirect;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\Success;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\Validation;
use Magento\Framework\ObjectManagerInterface;

class EvaluationResultFactory
{
    protected ObjectManagerInterface $objectManager;

    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * Clear non-successful states.
     *
     * @param array $details @deprecated object methods should be used.
     * @param string|null $event @deprecated object methods should be used.
     */
    public function createSuccess(array $details = [], ?string $event = null): Success
    {
        /** @var Success $result */
        $result = $this->objectManager->create(Success::class);

        // Backwards compatibility if-statements.
        if ($event) {
            $result->withCustomEvent($event);
        }
        if (! empty($details)) {
            $result->withDetails($details);
        }

        return $result;
    }

    /**
     * Prohibit user from proceeding and display the given flash message.
     *
     * @param string|null $text
     * @param string $messageType @deprecated backward compatibility - object methods should be used.
     * @param int|null $duration @deprecated backward compatibility - object methods should be used.
     * @param bool $blocking @deprecated backward compatibility - object methods should be used.
     */
    public function createErrorMessage(
        ?string $text = null,
        string $messageType = 'error',
        ?int $duration = null,
        bool $blocking = false
    ): ErrorMessage {
        /** @var ErrorMessage $result */
        $result = $this->objectManager->create(ErrorMessage::class);

        // Backwards compatibility if-statements.
        if ($text) {
            $result->withMessage($text);
        }
        if ($messageType === 'warning') {
            $result->asWarning();
        }
        if ($duration !== null) {
            $result->withVisibilityDuration($duration);
        }
        if ($blocking === true) {
            $result->asBlocking();
        }

        return $result;
    }

    /**
     * Prohibit user from proceeding, display the given flash message and dispatch the given JavaScript event.
     *
     * @param string|null $text
     * @param string|null $event @deprecated backward compatibility- object methods should be used.
     * @param string $messageType @deprecated backward compatibility - object methods should be used.
     * @param bool $blocking @deprecated backward compatibility - object methods should be used.
     */
    public function createErrorMessageEvent(
        ?string $text = null,
        ?string $event = null,
        string $messageType = 'error',
        bool $blocking = false
    ): ErrorEventMessage {
        /** @var ErrorEventMessage $result */
        $result = $this->objectManager->create(ErrorEventMessage::class);

        // Backwards compatibility if-statements.
        if ($text) {
            $result->withMessage($text);
        }
        if ($event) {
            $result->withCustomEvent($event);
        }
        if ($messageType) {
            $result->asCustomType($messageType);
        }
        if ($blocking) {
            $result->asBlocking();
        }

        return $result;
    }

    /**
     * Prohibit user from proceeding and dispatch the given JavaScript error event.
     *
     * @param array $details @deprecated backward compatibility - object methods should be used.
     * @param string|null $event @deprecated backward compatibility - object methods should be used.
     * @param bool $blocking @deprecated backward compatibility - object methods should be used.
     */
    public function createErrorEvent(
        array $details = [],
        ?string $event = null,
        bool $blocking = false
    ): ErrorEvent {
        /** @var ErrorEvent $result */
        $result = $this->objectManager->create(ErrorEvent::class);

        // Backwards compatibility if-statements.
        if ($event) {
            $result->withCustomEvent($event);
        }
        if ($blocking) {
            $result->asBlocking();
        }
        if (! empty($details)) {
            $result->withDetails($details);
        }

        return $result;
    }

    /**
     * Prohibit user from proceeding and dispatch the given JavaScript event.
     */
    public function createEvent(string $event): Event
    {
        /** @var Event $result */
        $result = $this->objectManager->create(Event::class);

        $result->withCustomEvent($event);

        return $result;
    }

    /**
     * Prohibit users from progressing with navigation tasks, such as advancing forward or placing an order.
     *
     * @deprecated This blocking evaluation result is deprecated as it silently obstructs primary navigation buttons.
     *              These buttons are essential for triggering navigational and validation tasks, which provide
     *              user-friendly notifications to guide customers on the next steps, such as proceeding or placing an order.
     */
    public function createBlocking(?string $cause = null): Blocking
    {
        /** @var Blocking $result */
        $result = $this->objectManager->create(Blocking::class);

        // Backwards compatibility if-statements.
        if ($cause) {
            $result->withCause($cause);
        }

        return $result;
    }

    /**
     * Execute a redirect.
     */
    public function createRedirect(string $url): Redirect
    {
        return $this->objectManager->create(Redirect::class, [
            'url' => $url
        ]);
    }

    /**
     * Include an additional navigation task.
     */
    public function createNavigationTask(string $name, EvaluationResult $task): NavigationTask
    {
        return $this->objectManager->create(NavigationTask::class, [
            'name' => $name,
            'task' => $task
        ]);
    }

    /**
     * Wrap multiple evaluation result types into a batch.
     *
     * @param EvaluationResult[] $evaluationResults
     */
    public function createBatch(array $evaluationResults = []): Batch
    {
        return $this->objectManager->create(Batch::class, [
            'evaluationResults' => $evaluationResults
        ]);
    }

    /**
     * Register an evaluation validator.
     */
    public function createValidation(string $name): Validation
    {
        return $this->objectManager->create(Validation::class, [
            'name' => $name
        ]);
    }

    /**
     * Register an evaluation executable.
     */
    public function createExecutable(string $name): Executable
    {
        return $this->objectManager->create(Executable::class, [
            'name' => $name
        ]);
    }

    /**
     * Register an evaluation message dialog.
     */
    public function createMessageDialog(string $title): MessageDialog
    {
        return $this->objectManager->create(MessageDialog::class, [
            'title' => $title
        ]);
    }

    /**
     * Log data within the browser console.
     */
    public function createConsoleLog(string $message): BrowserConsoleLog
    {
        return $this->objectManager->create(BrowserConsoleLog::class, [
            'message' => $message
        ]);
    }

    /**
     * Registers a custom evaluation result to be recognized by the frontend evaluation result processor.
     */
    public function createCustom(string $type): Custom
    {
        return $this->objectManager->create(Custom::class, [
            'type' => $type
        ]);
    }

    /**
     * @template T of EvaluationResult
     * @param class-string<T> $type
     * @return T
     */
    public function create(string $type, array $args = []): EvaluationResult
    {
        return $this->objectManager->create($type, $args);
    }
}

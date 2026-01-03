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
use Hyva\Checkout\Model\Magewire\Component\Evaluation\Concern\SequenceCapabilities;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\Concern\StackingCapabilities;
use Hyva\Checkout\Model\Magewire\Hydrator\Evaluation;
use Magewirephp\Magewire\Component;

class NavigationTask extends EvaluationResult
{
    use DetailsCapabilities;
    use StackingCapabilities;
    use SequenceCapabilities;

    public const TYPE = 'navigation_task';

    public const EXECUTION_SEQUENCE_BEFORE = 'before';
    public const EXECUTION_SEQUENCE_AFTER = 'after';

    public const DIRECTION_FORWARDS  = 'forwards';
    public const DIRECTION_BACKWARDS = 'backwards';

    private array $directions = [
        self::DIRECTION_FORWARDS
    ];

    private EvaluationResult $task;
    private Evaluation $evaluationHydrator;

    public function __construct(
        string $name,
        EvaluationResult $task,
        Evaluation $evaluationHydrator
    ) {
        $this->withName($name);
        $this->withTask($task);

        $this->evaluationHydrator = $evaluationHydrator;
    }

    public function getArguments(Component $component): array
    {
        $taskArguments = $this->evaluationHydrator->compileEvaluationResult($component, $this->task);
        // Needs to be forcefully dispatched instead of queued for waiting processes.
        $taskArguments['dispatch'] = true;

        return [
            'name' => $this->getName(),
            'stack' => [
                'position' => $this->stackPosition,
            ],
            'detail' => $this->getDetails($component),
            'type' => $this->task->getType(),
            'execution_sequence' => $this->executionSequence,
            'task' => $taskArguments,
            'directions' => $this->directions,
        ];
    }

    /**
     * Associates a task with the current instance.
     */
    public function withTask(EvaluationResult $task): self
    {
        $this->task = $task;

        return $this;
    }

    /**
     * Target the task to only execute when navigating forwards (default).
     */
    public function whenNavigatingForwards(): static
    {
        $this->directions = [self::DIRECTION_FORWARDS];

        return $this;
    }

    /**
     * Target the task to only execute when navigating backwards.
     */
    public function whenNavigatingBackwards(): static
    {
        $this->directions = [self::DIRECTION_BACKWARDS];

        return $this;
    }

    /**
     * Target the task to only execute when going either direction.
     */
    public function whenNavigatingBothWays(): static
    {
        $this->directions = [
            self::DIRECTION_FORWARDS,
            self::DIRECTION_BACKWARDS,
        ];

        return $this;
    }
}

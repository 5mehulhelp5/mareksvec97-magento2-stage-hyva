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
use Psr\Log\LoggerInterface;

class Validation extends EvaluationResult
{
    use DetailsCapabilities;
    use StackingCapabilities;
    use SequenceCapabilities;

    public const TYPE = 'validation';

    private string $name;
    private ?EvaluationResult $failureResult = null;

    private Evaluation $evaluationHydrator;
    private LoggerInterface $logger;

    public function __construct(
        string $name,
        Evaluation $evaluationHydrator,
        LoggerInterface $logger
    ) {
        $this->withName($name);

        $this->evaluationHydrator = $evaluationHydrator;
        $this->logger = $logger;
    }

    public function getArguments(Component $component): array
    {
        if ($this->failureResult) {
            $failureResultArguments = $this->evaluationHydrator->compileEvaluationResult($component, $this->failureResult);
            // Needs to be forcefully dispatched instead of queued for waiting processes.
            $failureResultArguments['dispatch'] = true;
        }

        return [
            'name' => $this->name,
            'detail' => $this->getDetails($component),
            'stack' => [
                'position' => $this->stackPosition,
            ],
            'execution_sequence' => $this->executionSequence,
            'results' => [
                'failure' => $failureResultArguments ?? null
            ]
        ];
    }

    /**
     * Assigns a validator name to the validation result for identification purposes.
     * This name should correspond to the registered frontend validator; if not found, it will be skipped.
     */
    public function withName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Includes a specific evaluation result along with a custom validator,
     * facilitating a formatted result to be processed upon validation failure.
     */
    public function withFailureResult(EvaluationResult $failureResult): self
    {
        $this->failureResult = $failureResult;

        return $this;
    }
}

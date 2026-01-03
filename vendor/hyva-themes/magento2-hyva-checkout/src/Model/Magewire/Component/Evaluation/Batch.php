<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Magewire\Component\Evaluation;

use Hyva\Checkout\Exception\EvaluationException;
use Hyva\Checkout\Model\Magewire\Component\EvaluationResultFactory;
use Hyva\Checkout\Model\Magewire\Hydrator\Evaluation;
use Magewirephp\Magewire\Component;

class Batch extends EvaluationResult
{
    public const TYPE = 'batch';

    private Evaluation $evaluationHydrator;
    /** @deprecated The factory is already in the evaluation result abstraction. */
    private EvaluationResultFactory $evaluationResultFactory;

    /** @var EvaluationResult[] $evaluationResults */
    private array $evaluationResults;

    public function __construct(
        Evaluation $evaluationHydrator,
        EvaluationResultFactory $evaluationResultFactory,
        array $evaluationResults = []
    ) {
        $this->evaluationHydrator = $evaluationHydrator;
        $this->evaluationResults = $evaluationResults;

        $this->evaluationResultFactory = $evaluationResultFactory;
    }

    /**
     * @throws EvaluationException
     */
    public function getArguments(Component $component): array
    {
        $arguments = [];

        foreach ($this->evaluationResults as $evaluationResult) {
            if (! $evaluationResult instanceof EvaluationResult) {
                throw new EvaluationException(
                    __('Evaluation result can only be of type %1', EvaluationResult::class)
                );
            }

            // As a batch shares characteristics with an evaluation result, we consolidate all result type
            // arguments and provide supplementary bulk data for use during the processing of these results.
            $arguments[] = $this->evaluationHydrator->compileEvaluationResult($component, $evaluationResult);
        }

        return $arguments;
    }

    /**
     * Push a result into the batch.
     */
    public function push(EvaluationResult $evaluationResult): self
    {
        if ($evaluationResult instanceof Batch) {
            return $this->spread($evaluationResult);
        }

        $this->evaluationResults[] = $evaluationResult;
        return $this;
    }

    /**
     * Push a result at the beginning of the batch.
     */
    public function unshift(EvaluationResult $result): static
    {
        array_unshift($this->evaluationResults, $result);

        return $this;
    }

    /**
     * @param array<mixed, EvaluationResult> $batch
     */
    public function merge(array $batch): self
    {
        foreach ($batch as $item) {
            $this->evaluationResults[] = $item;
        }

        return $this;
    }

    /**
     * Spread batched items into the current batch.
     */
    public function spread(Batch $evaluationBatchResult, bool $recursive = true): self
    {
        foreach ($evaluationBatchResult->toArray() as $item) {
            if ($recursive && $item instanceof Batch) {
                $this->spread($item);

                continue;
            }

            $this->push($item);
        }

        return $this;
    }

    /**
     * Returns the amount of batched evaluation results.
     */
    public function count(): int
    {
        return count($this->evaluationResults);
    }

    /**
     * Clear the current batch from existing evaluation results.
     *
     * Example:
     *
     *   Clear only those who are not an instance of redirect.
     *     fn ($value) => $value instanceof Redirect
     */
    public function clear(?callable $filter = null): self
    {
        if ($this->count() !== 0) {
            $this->evaluationResults = $filter ? array_filter($this->evaluationResults, fn ($item) => ! $filter($item)) : [];
        }

        return $this;
    }

    /**
     * Clears all results that have been assigned the specified alias.
     */
    public function clearByAlias(string $alias): static
    {
        return $this->clear(fn (EvaluationResult $result) => $result->getAlias() === $alias);
    }

    /**
     * Clears all results matching the given type.
     */
    public function clearByType(string $type): static
    {
        return $this->clear(fn (EvaluationResult $result) => $result->getType() === $type);
    }

    /**
     * Clears all results matching the given result.
     */
    public function clearByResult(bool $choice): static
    {
        return $this->clear(fn (EvaluationResult $result) => $result->getResult() === $choice);
    }

    /**
     * Clears all results matching the given tags.
     */
    public function clearByTags(array $tags): static
    {
        return $this->clear(fn (EvaluationResult $result) => $result->containsTags($tags));
    }

    /**
     * Filters and returns batch result items that meet the criteria defined by the given callable.
     */
    public function filter(callable $filter): array
    {
        return array_filter($this->evaluationResults, $filter);
    }

    /**
     * Check if the current batch owns a specific evaluation result
     * and optionally executes the callback if so.
     *
     * Example:
     *
     *   Check if there was already a Redirect set.
     *     fn ($value) => $value instanceof Redirect
     */
    public function owns(callable $filter, ?callable $callback = null): bool
    {
        $result = count($this->filter($filter)) > 0;

        if ($callback) {
            $callback($this);
        }

        return $result;
    }

    /**
     * Check if the current batch misses a specific evaluation result
     * and optionally executes the callback if so.
     *
     * Example:
     *
     *   Check if there was already a Redirect set.
     *     fn ($value) => $value instanceof Redirect
     */
    public function misses(callable $filter, ?callable $callback = null): bool
    {
        return ! $this->owns($filter, $callback);
    }

    /**
     * Walk over each evaluation result object.
     */
    public function walk(callable $callback, ?callable $filter = null): self
    {
        // Emulates the functionality of array_walk while incorporating filtering capabilities.
        foreach (array_filter($this->evaluationResults, $filter ?? fn () => true) as $item) {
            $callback($item);
        }

        return $this;
    }

    /**
     * Returns if the batch doesn't contain any results.
     */
    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    /**
     * Returns if the batch contains any results.
     */
    public function containsResults(): bool
    {
        return ! $this->isEmpty();
    }

    /**
     * Checks if any or some of the results in the provided array are failure results.
     */
    public function containsFailureResults(bool $strict = false): bool
    {
        $filter = fn (EvaluationResult $result) => ! $result->getResult();

        return $strict ? count($this->filter($filter)) === $this->count() : $this->owns($filter);
    }

    /**
     * Checks if any or some of the results in the provided array are successful results.
     */
    public function containsSuccessResults(bool $strict = false): bool
    {
        $filter = fn (EvaluationResult $result) => $result->getResult();

        return $strict ? count($this->filter($filter)) === $this->count() : $this->owns($filter);
    }

    /**
     * Enforces the dispatching of each detachable evaluation result, ensuring that any relevant
     * actions or events associated with the result are triggered appropriately.
     */
    public function dispatch(): self
    {
        $this->walk(static function (EvaluationResult $result) {
            if (method_exists($result, 'dispatch')) {
                $result->dispatch();
            }
        });

        return $this;
    }

    /**
     * @deprecated use the factory() method (from the abstract) instead.
     * Returns all items within the batch.
     */
    public function all(): array
    {
        return $this->evaluationResults;
    }

    public function factory(): EvaluationResultFactory
    {
        return $this->evaluationResultFactory;
    }

    private function toArray(): array
    {
        return $this->evaluationResults;
    }

    /**
     * @deprecated has been replaced with the factory method.
     * @see static::factory()
     */
    public function getFactory(): EvaluationResultFactory
    {
        return $this->factory();
    }
}

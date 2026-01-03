<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model;

use Hyva\Checkout\Model\Checkout\Step;

class Checkout
{
    private string $name = 'unknown';
    private string $label = 'Unknown';
    private ?string $parent;
    private bool $visible = false;
    private array $sequence = [];
    private string $hash = '50d8b4a941c26b89482c94ab324b5a274f9ced66';

    /** @var Step[] $steps */
    private array $steps = [];
    /** @var Step[] $availableSteps */
    private array $availableSteps = [];

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Returns the checkout name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel(string $label): self
    {
        $this->label = ucfirst($label);

        return $this;
    }

    /**
     * Returns the checkout label.
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Set all checkouts steps.
     *
     * @param \Hyva\Checkout\Model\Checkout\Step[] $steps
     */
    public function setSteps(array $steps): self
    {
        $this->steps = $steps;

        return $this;
    }

    /**
     * Returns an uncensored array of steps.
     *
     * @return \Hyva\Checkout\Model\Checkout\Step[]
     */
    public function getSteps(): array
    {
        return $this->steps;
    }

    /**
     * Refreshes the current available steps based on the provided or default step filter callable.
     */
    public function refreshAvailableSteps(?callable $filter = null): self
    {
        $filter ??= fn (Step $step) => $step->canInclude();
        $this->availableSteps = array_filter($this->getSteps(), fn (Step $step) => $filter($step));

        return $this;
    }

    /**
     * Returns a censored array of steps, including only those that are valid and accessible.
     *
     * @return \Hyva\Checkout\Model\Checkout\Step[]
     */
    public function getAvailableSteps(): array
    {
        return $this->availableSteps;
    }

    /**
     * Returns whether any steps are available.
     */
    public function hasSteps(): bool
    {
        return $this->countSteps() !== 0;
    }

    /**
     * Returns the number of available steps.
     */
    public function countSteps(): int
    {
        return count($this->getAvailableSteps());
    }

    /**
     * Returns whether a given step exists based on the specified route.
     */
    public function stepExistsByRoute(string $route): bool
    {
        return $this->getStepByRoute($route) !== null;
    }

    /**
     * Returns whether a given step exists based on the specified step object.
     */
    public function stepExists(Step $step): bool
    {
        return $this->stepExistsByRoute($step->getRoute());
    }

    /**
     * Returns whether all given steps exist.
     *
     * @param \Hyva\Checkout\Model\Checkout\Step[] $steps
     */
    public function stepsExist(array $steps): bool
    {
        return count($steps) === count(array_filter($steps, fn ($step) => $step instanceof Step && $this->stepExists($step)));
    }

    /**
     * Attempts to return a step object based on the specified step route.
     */
    public function getStepByRoute(string $route): ?Step
    {
        return $this->searchStep(fn (Step $step) => $step->getRoute() === $route);
    }

    /**
     * Tries to retrieve a step object based on the specified offset and direction (upwards or downwards).
     */
    public function getStepByOffset(Step $step, int $offset, $upwards = true): ?\Hyva\Checkout\Model\Checkout\Step
    {
        $availableSteps = $this->getAvailableSteps();
        $currentActive = $step->getName();

        $keys = array_keys($availableSteps);
        $currentIndex = array_search($currentActive, $keys);
        $index = $upwards ? ($currentIndex + $offset) : ($currentIndex - $offset);

        if ($next = $keys[$index] ?? null) {
            $next = $availableSteps[$next];
        }

        return $next;
    }

    /**
     * Returns all available steps filtered by the provided callable.
     *
     * @return \Hyva\Checkout\Model\Checkout\Step[]
     */
    public function searchSteps(callable $filter): array
    {
        return array_filter($this->getAvailableSteps(), $filter);
    }

    /**
     * Attempts to return an available step filtered by the provided callable.
     *
     * @return \Hyva\Checkout\Model\Checkout\Step|null
     */
    public function searchStep(callable $filter): ?\Hyva\Checkout\Model\Checkout\Step
    {
        $steps = $this->searchSteps($filter);

        if (count($steps) === 1) {
            return reset($steps) ?: null;
        }

        return null;
    }

    /**
     * Returns the index of the step based on the provided step object.
     */
    public function searchStepIndex(Step $step): ?int
    {
        $index = array_search($step->getName(), array_keys($this->getAvailableSteps()));

        return $index ?: null;
    }

    /**
     * @param null|string $parent
     * @return $this
     */
    public function setParent(?string $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getParent(): ?string
    {
        return $this->parent;
    }

    /**
     * @param bool $visible
     * @return $this
     */
    public function setVisible(bool $visible): self
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * @return bool
     */
    public function getVisible(): bool
    {
        return $this->visible;
    }

    /**
     * @param mixed $sequence
     * @return $this
     */
    public function setSequence(array $sequence): self
    {
        $this->sequence = $sequence;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSequence(): array
    {
        return $this->sequence;
    }

    /**
     * @param string $hash
     * @return $this
     */
    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * Attempts to return the first available step.
     */
    public function getFirstStep(): ?\Hyva\Checkout\Model\Checkout\Step
    {
        $steps = $this->getAvailableSteps();

        if (count($steps) === 0) {
            return null;
        }

        $step = reset($steps);
        
        return $step === false ? null : $step;
    }

    /**
     * Attempts to return the last available step.
     */
    public function getLastStep(): ?\Hyva\Checkout\Model\Checkout\Step
    {
        $steps = $this->getAvailableSteps();

        if (count($steps) === 0) {
            return null;
        }

        $step = end($steps);

        return $step === false ? null : $step;
    }

    /**
     * Determines if the two given steps are equal to each other.
     */
    public function isComparison(Step $a, Step $b, ?callable $comparator = null): bool
    {
        $comparator ??= fn (Step $a, Step $b): bool => $a->toPublicDataArray() === $b->toPublicDataArray();

        return $comparator($a, $b);
    }

    /**
     * Determines if the given step is the last available step.
     */
    public function isLastStep(Step $step): bool
    {
        $lastStep = $this->getLastStep();

        return $lastStep && $this->isComparison($step, $lastStep);
    }

    /**
     * Determines if the given step is the first available step.
     */
    public function isFirstStep(Step $step): bool
    {
        $firstStep = $this->getFirstStep();

        return $firstStep && $this->isComparison($step, $firstStep);
    }

    /**
     * Determines if the given step sits in between the first and the last step.
     */
    public function isInBetweenFirstLast(Step $step): bool
    {
        return ! $this->isFirstStep($step) && ! $this->isLastStep($step);
    }

    /**
     * Determines if the given step is a step forward based on its comparison.
     */
    public function isStepForwards(Step $current, Step $comparison): bool
    {
        return $current->getPosition() > $comparison->getPosition();
    }

    /**
     * Determines if the given step is a step backwards based on its comparison.
     */
    public function isStepBackwards(Step $current, Step $comparison): bool
    {
        return $current->getPosition() < $comparison->getPosition();
    }

    /**
     * Attempts to return the step preceding the given step.
     */
    public function getStepBefore(Step $step): ?\Hyva\Checkout\Model\Checkout\Step
    {
        return $this->stepExists($step) ? $this->getStepByOffset($step, 1, false) : null;
    }

    /**
     * Attempts to return the step following the given step.
     */
    public function getStepAfter(Step $step): ?\Hyva\Checkout\Model\Checkout\Step
    {
        return $this->stepExists($step) ? $this->getStepByOffset($step, 1) : null;
    }

    /**
     * Returns if the checkout is a single stepper based on the available steps.
     */
    public function isSingleStepper(): bool
    {
        return $this->countSteps() === 1;
    }

    /**
     * Returns if the checkout is a multi stepper based on the available steps.
     */
    public function isMultiStepper(): bool
    {
        return ! $this->isSingleStepper();
    }
}

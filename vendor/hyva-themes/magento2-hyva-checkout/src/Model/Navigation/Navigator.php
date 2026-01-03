<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Navigation;

use Hyva\Checkout\Exception\NavigatorException;
use Hyva\Checkout\Model\Checkout;
use Hyva\Checkout\Model\CheckoutFactory;
use Magento\Framework\Exception\LocalizedException;

class Navigator
{
    private CheckoutFactory $checkoutFactory;
    private NavigatorMemory $navigatorMemory;
    private NavigatorHistory $navigatorHistory;
    private NavigatorInstructionsFactory $navigatorInstructionsFactory;
    private NavigatorConfig $navigatorConfig;

    private ?Checkout $checkout = null;
    private ?Checkout\Step $step = null;

    /** @var Checkout[] $checkouts */
    private array $checkouts = [];

    private bool $running = false;
    private bool $finished = false;

    public function __construct(
        CheckoutFactory $checkoutFactory,
        NavigatorMemory $navigatorMemory,
        NavigatorHistory $navigatorHistory,
        NavigatorInstructionsFactory $navigatorInstructionsFactory,
        NavigatorConfig $navigatorConfig
    ) {
        $this->checkoutFactory = $checkoutFactory;
        $this->navigatorMemory = $navigatorMemory;
        $this->navigatorHistory = $navigatorHistory;
        $this->navigatorInstructionsFactory = $navigatorInstructionsFactory;
        $this->navigatorConfig = $navigatorConfig;
    }

    /**
     * @throws LocalizedException
     */
    public function start(?NavigatorInstructions $instructions = null): self
    {
        $attempts = $this->getMemory()->getAttempts();
        $attempts++;

        $this->getMemory()->setData('attempts', $attempts, true);

        return $this->restart($instructions ?? $this->createInstructions(), true);
    }

    /**
     * @throws LocalizedException
     * @throws NavigatorException
     */
    public function restart(NavigatorInstructions $instructions, bool $force = false): self
    {
        if ($this->isRunning() && $force === false) {
            return $this;
        }

        // Re-determine the given instructions to support backward compatibility.
        $instructions = $this->reset()->determineInstructions($instructions);

        $checkout = $this->setActiveCheckout(
            $this->checkouts[$instructions->getCheckout()] ?? $this->checkoutFactory->create($instructions->getCheckout())
        );

        if ($instructions->hasStep() && $this->canStepTo($instructions->getStep())) {
            $this->stepToByRoute($instructions->getStep());
        } else {
            $this->stepTo($checkout->getFirstStep());
        }

        $this->checkouts[$checkout->getName()] = $checkout;
        $this->running = true;

        return $this;
    }

    /**
     * Refresh metadata without losing the checkout state.
     */
    public function refresh(): self
    {
        // Clear all made steps in the cycle.
        $this->getHistory()->refresh();
        // Forget any navigation memories.
        $this->getMemory()->forget();

        return $this;
    }

    /**
     * Resets to its initial state.
     */
    public function reset(): self
    {
        $this->refresh();

        $this->checkout = null;
        $this->step = null;

        return $this;
    }

    /**
     * Flag the current navigation cycle as finished.
     */
    public function finish(): self
    {
        if ($this->isFinished()) {
            return $this;
        }

        $latest = $this->getHistory()->getLatest();
        $this->refresh();

        if ($latest) {
            $this->stepTo($latest);
        }

        $this->finished = true;
        return $this;
    }

    /**
     * Returns if the current navigation cycle is flagged as finished.
     */
    public function isFinished(): bool
    {
        return $this->finished;
    }

    /**
     * Returns if the current instance is flagged as running.
     */
    public function isRunning(): bool
    {
        return $this->running;
    }

    /**
     * Returns if the current instance is flagged as not running.
     */
    public function isNotRunning(): bool
    {
        return ! $this->isRunning();
    }

    public function getMemory(): NavigatorMemory
    {
        return $this->navigatorMemory;
    }

    public function getHistory(): NavigatorHistory
    {
        return $this->navigatorHistory;
    }

    public function getConfig(): NavigatorConfig
    {
        return $this->navigatorConfig;
    }

    public function setActiveCheckout(Checkout $checkout): Checkout
    {
        $this->checkouts[$checkout->getName()] = $checkout;
        $this->checkout = $checkout;

        return $checkout;
    }

    public function getActiveCheckout(): Checkout
    {
        if ($this->hasActiveCheckout() === false) {
            $this->checkout = $this->checkoutFactory->create();
        }

        return $this->checkout;
    }

    public function hasActiveCheckout(): bool
    {
        return $this->checkout !== null;
    }

    public function getActiveStep(): Checkout\Step
    {
        if ($this->step) {
            return $this->step;
        }

        $checkout = $this->getActiveCheckout();
        $step = $this->navigatorHistory->getLatest();

        if (! $step) {
            $step = $checkout->getFirstStep();
        }

        $this->step = $step;
        return $this->step;
    }

    public function hasActiveStep(): bool
    {
        return $this->step !== null;
    }

    public function stepForward(int $offset = 1): Checkout\Step
    {
        $current = $this->getActiveStep();
        $target = $this->getActiveCheckout()->getStepByOffset($current, $offset);

        if ($target) {
            return $this->stepTo($target);
        }

        return $current;
    }

    public function stepBackward(int $offset = 1): Checkout\Step
    {
        $current = $this->getActiveStep();
        $target = $this->getActiveCheckout()->getStepByOffset($current, $offset, false);

        if ($target) {
            return $this->stepTo($target);
        }

        return $current;
    }

    /**
     * @param Checkout\Step|string $step
     */
    public function canStepTo($step): bool
    {
        if ($step instanceof Checkout\Step) {
            $step = $step->getRoute();
        }

        if (is_string($step)) {
            return $this->getActiveCheckout()->stepExistsByRoute($step);
        }

        return false;
    }

    /**
     * @param Checkout\Step $step
     * @return Checkout\Step|null
     */
    public function stepTo(Checkout\Step $step): ?Checkout\Step
    {
        // Let's stay on the current step if the given step doesn't exist.
        if (! $this->getActiveCheckout()->stepExistsByRoute($step->getRoute())) {
            return $this->getActiveStep();
        }

        // Move to the new step.
        $this->step = $step;
        // Memorize this movement for the current session.
        $this->navigatorHistory->push($step);

        return $step;
    }

    public function stepToByRoute(string $route): ?Checkout\Step
    {
        $step = $this->getActiveCheckout()->searchStep(
            fn (Checkout\Step $step) => $route === $step->getRoute()
        );

        if ($step === null) {
            $step = $this->getActiveStep();
        }

        return $this->stepTo($step);
    }

    public function createInstructions(array $data = []): NavigatorInstructions
    {
        return $this->navigatorInstructionsFactory->create($data);
    }

    /**
     * Determine the appropriate checkout process to load. Factors to consider include
     * backward compatibility with previous versions, specific customer requirements,
     * and any ongoing promotional campaigns.
     */
    private function determineInstructions(NavigatorInstructions $instructions): NavigatorInstructions
    {
        /*
         * 1. When both checkout and step are already set, we assume they are correct
         *    and do not need to use any fallback options.
         */
        if ($instructions->hasCheckout() && $instructions->hasStep()) {
            return $instructions;
        }

        /*
         * 2. When only the checkout is defined and the step is not, we assume the step
         *    can be retrieved from the deprecated session.
         */
        if ($instructions->hasCheckout() && ! $instructions->hasStep()) {
            $obsolete = $this->getMemory()->getObsoleteCheckoutSession();

            if ($current = $obsolete->getCurrentStep('route')) {
                return $instructions->setStep($current);
            }
        }

        /*
         * 3. When only the step is defined and the checkout is not, we assume the checkout
         *    can be retrieved from the deprecated session.
         */
        if ($instructions->hasStep() && ! $instructions->hasCheckout()) {
            $obsolete = $this->getMemory()->getObsoleteCheckoutSession();

            if ($current = $obsolete->getCheckoutNamespace()) {
                return $instructions->setCheckout($current);
            }
        }

        /*
         * 4. When none of the above matched its criteria, we need to make sure we give
         *    the navigator some instructions.
         */
        return $instructions->setCheckout($this->getConfig()->getDefaultCheckout());
    }
}

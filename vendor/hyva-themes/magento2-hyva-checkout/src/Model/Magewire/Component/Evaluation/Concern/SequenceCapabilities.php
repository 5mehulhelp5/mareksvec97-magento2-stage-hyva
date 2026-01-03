<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Magewire\Component\Evaluation\Concern;

trait SequenceCapabilities
{
    private string $executionSequence = 'before';
    private bool $executionSequenceLock = false;

    /**
     * Ensures the execution of the task before proceeding with navigation or order placement.
     *
     * @param bool $lock Locks the execution point, preventing alterations.
     *
     * @return static till PHP8.x
     * @doc https://github.com/php/php-src/pull/5062
     */
    public function executeBefore(bool $lock = false): self
    {
        if ($this->executionSequenceLock) {
            return $this;
        }

        $this->executionSequence = 'before';

        if ($lock) {
            $this->lockExecutionSequence();
        }

        return $this;
    }

    /**
     * Ensures the execution of the task after proceeding with navigation or order placement.
     *
     * @param bool $lock Locks the execution point, preventing alterations.
     *
     * @return static till PHP8.x
     * @doc https://github.com/php/php-src/pull/5062
     */
    public function executeAfter(bool $lock = false): self
    {
        if ($this->executionSequenceLock) {
            return $this;
        }

        $this->executionSequence = 'after';

        if ($lock) {
            $this->lockExecutionSequence();
        }

        return $this;
    }

    /**
     * Prevents execution sequence changes.
     *
     * @return static till PHP8.x
     * @doc https://github.com/php/php-src/pull/5062
     */
    private function lockExecutionSequence(): self
    {
        $this->executionSequenceLock = true;

        return $this;
    }
}

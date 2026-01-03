<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Magewire\Component\Evaluation\Concern;

use Hyva\Checkout\Model\Magewire\Component\Evaluation\Blocking;

/**
 * @deprecated This blocking capability is deprecated as it silently obstructs primary navigation buttons.
 *             These buttons are essential for triggering navigational and validation tasks, which provide
 *             user-friendly notifications to guide customers on the next steps, such as proceeding or placing an order.
 * @see Blocking
 */
trait BlockingCapabilities
{
    private bool $blocking = false;
    private string $cause = '';

    /**
     * @deprecated Blocking capabilities have been removed as they are no longer considered user-friendly.
     * @see Blocking
     */
    public function withCause(string $cause)
    {
        $this->cause = ucfirst($cause);

        return $this;
    }

    /**
     * @deprecated Blocking capabilities have been removed as they are no longer considered user-friendly.
     * @see Blocking
     */
    public function hasCause(): bool
    {
        return strlen($this->cause) > 0;
    }

    /**
     * @deprecated Blocking capabilities have been removed as they are no longer considered user-friendly.
     * @see Blocking
     */
    public function getCause(): string
    {
        return $this->cause;
    }

    /**
     * @deprecated Blocking capabilities have been removed as they are no longer considered user-friendly.
     * @see Blocking
     */
    public function asBlocking()
    {
        return $this;
    }

    /**
     * @deprecated Blocking capabilities have been removed as they are no longer considered user-friendly.
     * @see Blocking
     */
    public function isBlocking(): bool
    {
        return false;
    }

    /**
     * @deprecated Blocking capabilities have been removed as they are no longer considered user-friendly.
     * @see Blocking
     */
    protected function getBlockingArguments(): array
    {
        return [
            'result' => $this->blocking,
            'cause' => strlen($this->cause) === 0 ? null : $this->cause
        ];
    }
}

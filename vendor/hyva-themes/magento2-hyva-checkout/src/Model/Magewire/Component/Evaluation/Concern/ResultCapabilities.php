<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Magewire\Component\Evaluation\Concern;

/**
 * The result capability allows an Evaluation Result to modify its final outcome, which is essential for determining the
 * overall Evaluation status. An Evaluation Result can be flagged as either positive or negative:
 *
 * - Positive Result: Indicates that the outcome does not block any actions on the frontend. It can act as a cleaner
 *   for a previous falsy result or serve as a positive confirmation. This result allows navigation to continue, such as
 *   proceeding to the next step or placing an order.
 *
 * - Negative Result: Signifies that something went wrong, independent of the frontend implementation. A negative
 *   result prevents further navigation, meaning the user cannot proceed to the next step or complete the order.
 *
 * Note: A falsy result typically blocks navigation, ensuring that errors are addressed before proceeding.
 */
trait ResultCapabilities
{
    private bool $result = false;

    /**
     * Flag the result dynamically to either true or false.
     */
    public function withResult(bool $result): self
    {
        $this->result = $result;

        return $this;
    }

    /**
     * Flag the result as false.
     */
    public function withFalseResult(): self
    {
        return $this->withResult(false);
    }

    /**
     * Flag the result as true.
     */
    public function withTrueResult(): self
    {
        return $this->withResult(true);
    }

    /**
     * Returns the current set result.
     *
     * Overrides the parent method to return the dynamically set result
     * rather than a fixed or default value.
     */
    public function getResult(): bool
    {
        return $this->result;
    }
}

<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model;

class CustomConditionProcessor
{
    /**
     * @param CustomConditionInterface $condition
     * @param string $method
     * @return bool
     */
    public function isApplicable(CustomConditionInterface $condition, string $method): bool
    {
        return $this->process($condition, $method) === true;
    }

    /**
     * @param CustomConditionInterface $condition
     * @param string $method
     * @return bool
     */
    public function isNotApplicable(CustomConditionInterface $condition, string $method): bool
    {
        return $this->process($condition, $method) === false;
    }

    /**
     * TODO surround the condition processing with a try-catch
     *      since CustomConditions can throw exceptions.
     *
     * @param CustomConditionInterface $condition
     * @param string $method
     * @return bool
     */
    protected function process(CustomConditionInterface $condition, string $method): bool
    {
        return $condition->{$method}();
    }
}

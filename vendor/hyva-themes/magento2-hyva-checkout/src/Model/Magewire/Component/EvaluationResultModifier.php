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
use Hyva\Checkout\Model\Magewire\Component\Evaluation\EvaluationResult;
use Magewirephp\Magewire\Component;

abstract class EvaluationResultModifier
{
    /**
     * Apply modifications to the given evaluation result batch.
     */
    abstract public function apply(Batch $batch, Component $component): EvaluationResult;
}

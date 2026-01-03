<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Magewire\Component;

use Hyva\Checkout\Model\Magewire\Component\Evaluation\EvaluationResult;
use Magewirephp\Magewire\Component;

class EvaluationResultManagement
{
    public function __construct(
        private readonly EvaluationResultFactory $evaluationResultFactory,
        private readonly array $evaluationResultModifiers = [],
    ) {
        //
    }

    public function processModificationsForComponent(Component $component, EvaluationResult|null $result = null): Evaluation\Batch
    {
        $modifiers = $this->listModifiersForComponent($component);
        $batch = $this->evaluationResultFactory->createbatch()->withTag('__wrapper');

        if ($result) {
            $batch->unshift($result->withTag('__origin'));
        }

        foreach ($modifiers as $modifier) {
            $modifier->apply($batch, $component);
        }

        return $batch;
    }

    /**
     * @return array<int, EvaluationResultModifier>
     */
    public function listModifiersForComponent(Component $component): array
    {
        $modifiers = $this->evaluationResultModifiers[$component->id] ?? [];
        $modifiers = is_array($modifiers) ? $modifiers : [];

        return array_filter($modifiers, fn ($modifier) => $modifier instanceof EvaluationResultModifier);
    }
}

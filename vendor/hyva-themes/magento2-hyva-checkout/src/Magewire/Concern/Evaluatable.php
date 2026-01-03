<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Magewire\Concern;

use Hyva\Checkout\Model\Magewire\Component\Evaluation\Batch;
use Magento\Framework\App\ObjectManager;

trait Evaluatable
{
    private Batch|null $evaluationBatch = null;

    protected function evaluationBatch(): Batch
    {
        return $this->evaluationBatch ??= ObjectManager::getInstance()->create(Batch::class);
    }
}

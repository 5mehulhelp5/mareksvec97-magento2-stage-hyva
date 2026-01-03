<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model;

use Hyva\Checkout\Model\Magewire\Component\EvaluationResultInterface;

/**
 * @api
 */
interface CustomValidatorInterface extends CustomConditionInterface
{
    public function getError(): EvaluationResultInterface;
}

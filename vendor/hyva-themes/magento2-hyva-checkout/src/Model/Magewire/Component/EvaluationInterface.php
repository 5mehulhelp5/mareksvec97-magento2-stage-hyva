<?php
/**
 * @author Hyvä Themes <info@hyva.io>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace Hyva\Checkout\Model\Magewire\Component;

/**
 * @api
 */
interface EvaluationInterface
{
    public function evaluateCompletion(EvaluationResultFactory $resultFactory): EvaluationResultInterface;
}

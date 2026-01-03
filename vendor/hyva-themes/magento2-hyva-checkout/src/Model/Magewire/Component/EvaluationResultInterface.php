<?php
/**
 * @author Hyvä Themes <info@hyva.io>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace Hyva\Checkout\Model\Magewire\Component;

use Magewirephp\Magewire\Component;

/**
 * @deprecated has been replaced with EvaluationResult to prevent backward incompatible changes.
 * @see \Hyva\Checkout\Model\Magewire\Component\Evaluation\EvaluationResult
 */
interface EvaluationResultInterface
{
    /**
     * All data to be used inside the evaluation processor.
     */
    public function getArguments(Component $component): array;

    /**
     * Returns if the evaluation was a success yes/no.
     */
    public function getResult(): bool;

    /**
     * Returns the type (processor) name of the result. (format example: 'foo_bar')
     */
    public function getType(): string;

    /**
     * Returns if an evaluation results needs to be marked as blocking.
     *
     * @deprecated use the blocking capabilities trait instead.
     * @see BlockingCapabilities
     */
    public function isBlocking(): bool;
}

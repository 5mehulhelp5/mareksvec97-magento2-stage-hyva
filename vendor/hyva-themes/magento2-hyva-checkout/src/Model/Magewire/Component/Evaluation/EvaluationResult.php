<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Magewire\Component\Evaluation;

use Hyva\Checkout\Model\Magewire\Component\Evaluation\Concern\AliasCapabilities;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\Concern\BlockingCapabilities;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\Concern\NamingCapabilities;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\Concern\TaggingCapabilities;
use Hyva\Checkout\Model\Magewire\Component\EvaluationResultFactory;
use Hyva\Checkout\Model\Magewire\Component\EvaluationResultInterface;
use Magento\Framework\App\ObjectManager;
use Magewirephp\Magewire\Component;

/**
 * Implements EvaluationResultInterface to avoid backward incompatibility.
 */
abstract class EvaluationResult implements EvaluationResultInterface
{
    public const TYPE = 'abstract';

    use NamingCapabilities;
    use AliasCapabilities;
    use TaggingCapabilities;

    private EvaluationResultFactory|null $factory = null;

    /**
     * Packs all user-defined elements into a single, strictly structured array. The composition of this array
     * can vary for each evaluation result, depending on specific requirements and additional public methods.
     *
     * Best practice is to avoid removing arguments to minimize backward compatibility changes. Instead, extend
     * functionality by adding additional arguments as needed.
     */
    abstract public function getArguments(Component $component): array;

    /**
     * Represents a unique identifier for categorizing evaluation results. This 'type' serves as a crucial
     * indicator, particularly for frontend components, allowing them to initialize appropriately based on
     * the specific result type returned by this method.
     */
    public function getType(): string
    {
        return $this::TYPE;
    }

    /**
     * Returns if the result should be seen as either a successful or as a failure.
     */
    public function getResult(): bool
    {
        return true;
    }

    /**
     * @deprecated use the blocking capabilities trait instead.
     * @see BlockingCapabilities, Blocking
     */
    public function isBlocking(): bool
    {
        return false;
    }

    /**
     * Returns the global evaluation result factory.
     */
    public function factory(): EvaluationResultFactory
    {
        return $this->factory ??= ObjectManager::getInstance()->get(EvaluationResultFactory::class); // phpcs:ignore
    }
}

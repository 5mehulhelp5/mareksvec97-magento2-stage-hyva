<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Magewire\Component\Evaluation;

use Hyva\Checkout\Model\Magewire\Component\Evaluation\Concern\BlockingCapabilities;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\Concern\DetailsCapabilities;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\Concern\DispatchCapabilities;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\Concern\ResultCapabilities;
use Magewirephp\Magewire\Component;

/**
 * A custom Evaluation Result is typically used for non-reusable scenarios, meaning it is tailored for a specific
 * use case. Custom results offer a comprehensive set of tools, incorporating most core capabilities while returning
 * specific data alongside them. The `type` is required for the corresponding frontend component, which can be
 * registered using the `hyvaCheckout.registerProcessor('{type}')` method.
 */
class Custom extends EvaluationResult
{
    use DetailsCapabilities;
    use BlockingCapabilities;
    use DispatchCapabilities;
    use ResultCapabilities;

    private string $type;
    private array $arguments = [];

    public function __construct(
        string $type
    ) {
        $this->type = $type;
    }

    public function withArguments(array $arguments, bool $merge = true): self
    {
        $this->arguments = $merge ? array_merge($this->arguments, $arguments) : $arguments;

        return $this;
    }

    public function getArguments(Component $component): array
    {
        return array_merge($this->arguments, [
            'details' => $this->getDetails($component),
            'blocking' => $this->getBlockingArguments()
        ]);
    }

    public function getType(): string
    {
        return $this->type;
    }
}

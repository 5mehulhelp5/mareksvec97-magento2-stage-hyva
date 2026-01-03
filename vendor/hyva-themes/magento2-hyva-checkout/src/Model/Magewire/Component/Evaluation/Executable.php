<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Magewire\Component\Evaluation;

use Hyva\Checkout\Model\Magewire\Component\Evaluation\Concern\DispatchCapabilities;
use Magewirephp\Magewire\Component;

class Executable extends EvaluationResult
{
    use DispatchCapabilities;

    public const TYPE = 'executable';

    private array $params = [];

    public function __construct(
        string $name
    ) {
        $this->withName($name);
    }

    public function getArguments(Component $component): array
    {
        return [
            'name' => $this->getName(),
            'params' => $this->params
        ];
    }

    public function withParam(string $key, $value): self
    {
        $this->params[$key] = $value;

        return $this;
    }

    public function withParams(array $params): self
    {
        $this->params = array_merge($this->params, $params);

        return $this;
    }
}

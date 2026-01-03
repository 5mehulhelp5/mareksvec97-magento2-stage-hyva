<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Checkout;

use Exception;
use Hyva\Checkout\Model\CustomConditionFactory;
use Hyva\Checkout\Model\CustomConditionInterface;
use Hyva\Checkout\Model\CustomConditionProcessor;

class StepConditions
{
    private CustomConditionFactory $customConditionFactory;
    private CustomConditionProcessor $customConditionProcessor;

    /** @var array[] */
    private array $items;
    /** @var CustomConditionInterface[] */
    private array $validators = [];

    public function __construct(
        CustomConditionFactory $customConditionFactory,
        CustomConditionProcessor $customConditionProcessor,
        array $items = []
    ) {
        $this->customConditionFactory = $customConditionFactory;
        $this->customConditionProcessor = $customConditionProcessor;
        $this->items = $items;
    }

    public function assertSuccess(): bool
    {
        try {
            foreach ($this->getItems() as $condition) {
                $instance = $this->getValidatorByType($condition['type']);

                if ($this->customConditionProcessor->isNotApplicable($instance, $condition['method'])) {
                    return false;
                }
            }
        } catch (Exception) {
            return false;
        }

        return true;
    }

    public function assertFailure(): bool
    {
        return ! $this->assertSuccess();
    }

    public function setItems(array $items): self
    {
        $this->items = $items;

        return $this;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    private function getValidatorByType(string $type): ?CustomConditionInterface
    {
        if (! isset($this->validators[$type])) {
            $this->validators[$type] = $this->customConditionFactory->create($type);
        }

        return $this->validators[$type];
    }
}
